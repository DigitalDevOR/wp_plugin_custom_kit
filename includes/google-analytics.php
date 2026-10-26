<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Auth\Credentials\ServiceAccountCredentials;

/**
 * Restituisce il percorso del file di credenziali caricato dal pannello.
 */
function widget_loco_google_credentials_file(): string
{
    return WIDGET_LOCO_PATH . 'includes/private-google-analytics/credentials.json';
}

/**
 * Restituisce la media mensile degli utenti totali degli ultimi tre mesi completi.
 *
 * @param string $property_id ID della proprietà Google Analytics 4.
 *
 * @return float|null Media mensile, oppure null se non è disponibile.
 */
function widget_loco_get_media_mensile_utenti_totale(string $property_id, ?string &$error_message = null): ?float
{
    $error_message = null;
    $property_id = trim($property_id);
    if ($property_id === '') {
        $error_message = 'Property ID GA4 mancante.';
        return null;
    }
    if (!ctype_digit($property_id)) {
        $error_message = 'Il Property ID GA4 deve contenere solo cifre.';
        error_log('Widget Banner: ' . $error_message);
        return null;
    }

    $cache_key = 'widget_loco_ga4_users_' . md5($property_id);
    $cached_value = get_transient($cache_key);
    if ($cached_value !== false && is_numeric($cached_value)) {
        return (float) $cached_value;
    }

    $timezone = new DateTimeZone('Europe/Rome');
    $current_month = new DateTimeImmutable('first day of this month', $timezone);
    $start = $current_month->modify('-3 months');
    $end = $current_month->modify('-1 day');
    $client = null;

    try {
        $client_options = [
            'transport' => 'rest',
        ];
        $credentials_file = widget_loco_google_credentials_file();
        if (!is_readable($credentials_file)) {
            throw new \RuntimeException(
                'File delle credenziali Google assente o non leggibile in includes/private-google-analytics/credentials.json.'
            );
        }

        $credentials_json = file_get_contents($credentials_file);
        $credentials_data = is_string($credentials_json)
            ? json_decode($credentials_json, true)
            : null;
        if (!is_array($credentials_data)) {
            throw new \UnexpectedValueException('Il file delle credenziali Google non contiene JSON valido.');
        }

        $client_options['credentials'] = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/analytics.readonly'],
            $credentials_data
        );

        $client = new BetaAnalyticsDataClient($client_options);

        $request = (new RunReportRequest())
            ->setProperty('properties/' . $property_id)
            ->setDateRanges([
                new DateRange([
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                ]),
            ])
            ->setDimensions([
                new Dimension(['name' => 'yearMonth']),
            ])
            ->setMetrics([
                new Metric(['name' => 'totalUsers']),
            ]);

        $response = $client->runReport($request);
        $months = [];

        for ($i = 0; $i < 3; $i++) {
            $key = $start->modify("+{$i} months")->format('Ym');
            $months[$key] = 0;
        }

        foreach ($response->getRows() as $row) {
            $key = $row->getDimensionValues()[0]->getValue();
            if (array_key_exists($key, $months)) {
                $months[$key] = (int) $row->getMetricValues()[0]->getValue();
            }
        }

        $average = round(array_sum($months) / 3, 2);
        set_transient($cache_key, $average, 6 * HOUR_IN_SECONDS);

        return $average;
    } catch (\Throwable $exception) {
        $error_message = $exception->getMessage();
        error_log('Widget Banner: errore nel recupero dei dati Google Analytics per la proprietà '
            . $property_id . ': ' . $error_message);

        return null;
    } finally {
        if ($client !== null) {
            $client->close();
        }
    }
}
