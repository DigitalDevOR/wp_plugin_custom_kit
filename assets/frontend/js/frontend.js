(() => {
    const devices = {
        desktop: {label: "Desktop", title: "Formati <em>desktop</em>", context: "da computer"},
        mobile: {label: "Mobile", title: "Formati <em>mobile</em>", context: "da smartphone"}
    };
    const categories = {
        standard: "Standard",
        impact: "Impact",
        video: "Video e audio",
        native: "Native",
        sponsor: "Sponsorship"
    };
    const formatVisuals = {
        "d-masthead": {slots:[[38,42,116,30]], anim:"pulse"},
        "d-mpu": {slots:[[156,44,50,42]], anim:"pulse"},
        "d-halfpage": {slots:[[158,44,46,96]], anim:"pulse"},
        "d-sticky": {slots:[[21,130,198,19,0]], anim:"slide"},
        "d-inter": {overlay:true, slots:[[80,38,80,60]], anim:"pop"},
        "d-video": {slots:[[34,80,106,60]], anim:"pulse"},
        "d-native": {style:"native", slots:[[34,100,54,42]], anim:"pulse"},
        "d-takeover": {slots:[[38,42,112,28],[156,44,50,34],[158,83,48,59]], anim:"pulse"},
        "m-top": {slots:[[38,26,44,14]], anim:"pulse"},
        "m-mpu": {slots:[[40,70,40,33]], anim:"pulse"},
        "m-halfpage": {slots:[[41,62,38,76]], anim:"pulse"},
        "m-sticky": {slots:[[36,176,48,10,0]], anim:"slide"},
        "m-inter": {overlay:true, slots:[[41,58,38,57]], anim:"pop"},
        "m-video": {slots:[[40,70,40,22.5]], anim:"pulse"},
        "m-native": {style:"native", slots:[[40,116,40,30]], anim:"pulse"},
        "m-takeover": {slots:[[38,26,44,14],[40,70,40,33],[36,176,48,10,0]], anim:"pulse"}
    };
    const viewBoxes = {desktop: "0 0 240 160", mobile: "0 0 120 200"};
    const icons = {
        desktop: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
        mobile: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/></svg>'
    };
    const escapeHtml = value => String(value).replace(/[&<>"]/g, char => ({
        "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;"
    })[char]);
    const formatPrice = value => new Intl.NumberFormat("it-IT", {
        style: "currency",
        currency: "EUR",
        maximumFractionDigits: 2
    }).format(value);

    function baseDesktop() {
        return `
            <rect class="s-frame" x="20" y="10" width="200" height="140" rx="5"/>
            <rect class="s-bar" x="20.4" y="10.4" width="199.2" height="8" rx="4.6"/>
            <circle class="s-dot" cx="27" cy="14.4" r="1.5"/><circle class="s-dot" cx="32" cy="14.4" r="1.5"/><circle class="s-dot" cx="37" cy="14.4" r="1.5"/>
            <rect class="s-bs" x="106" y="25" width="28" height="7" rx="1.5"/>
            <rect class="s-b" x="70" y="35" width="100" height="3" rx="1.5"/>
            <rect class="s-b" x="34" y="44" width="116" height="32" rx="2"/>
            <rect class="s-b" x="34" y="81" width="116" height="3" rx="1.5"/><rect class="s-b" x="34" y="87" width="100" height="3" rx="1.5"/><rect class="s-b" x="34" y="93" width="108" height="3" rx="1.5"/>
            <rect class="s-b" x="34" y="100" width="54" height="42" rx="2"/><rect class="s-b" x="94" y="100" width="56" height="42" rx="2"/>
            <rect class="s-b" x="156" y="44" width="50" height="34" rx="2"/>
            <rect class="s-b" x="156" y="83" width="50" height="3" rx="1.5"/><rect class="s-b" x="156" y="89" width="40" height="3" rx="1.5"/>
            <rect class="s-b" x="156" y="96" width="50" height="46" rx="2"/>`;
    }

    function baseMobile() {
        return `
            <rect class="s-device" x="33" y="4" width="54" height="192" rx="9"/>
            <rect class="s-frame" x="36" y="14" width="48" height="172" rx="2.5"/>
            <rect class="s-dot" x="53" y="8" width="14" height="2.6" rx="1.3"/>
            <rect class="s-bar" x="36" y="14" width="48" height="10"/>
            <rect class="s-bs" x="52" y="17" width="16" height="4" rx="1"/>
            <rect class="s-b" x="40" y="28" width="40" height="26" rx="1.5"/>
            <rect class="s-b" x="40" y="58" width="40" height="2.5" rx="1.2"/><rect class="s-b" x="40" y="63" width="32" height="2.5" rx="1.2"/>
            <rect class="s-b" x="40" y="70" width="40" height="30" rx="1.5"/>
            <rect class="s-b" x="40" y="104" width="40" height="2.5" rx="1.2"/><rect class="s-b" x="40" y="109" width="34" height="2.5" rx="1.2"/>
            <rect class="s-b" x="40" y="116" width="40" height="30" rx="1.5"/>
            <rect class="s-b" x="40" y="150" width="40" height="2.5" rx="1.2"/>
            <rect class="s-b" x="40" y="156" width="40" height="26" rx="1.5"/>`;
    }

    function formatSvg(format) {
        const visual = formatVisuals[format.id] || {slots: [], anim: "pulse"};
        const base = format.dev === "desktop" ? baseDesktop() : baseMobile();
        const scrim = !visual.overlay ? "" : (format.dev === "desktop"
            ? '<rect class="s-scrim" x="20" y="10" width="200" height="140" rx="5"/>'
            : '<rect class="s-scrim" x="36" y="14" width="48" height="172" rx="2.5"/>');
        const slotClass = visual.style === "native" ? "s-native" : "s-slot";
        const slots = visual.slots.map(([x, y, width, height, radius]) => {
            const corner = radius === undefined ? 1.6 : radius;
            const fontSize = Math.min(height * .42, width * .22, 11);
            const label = width >= 14 && height >= 8
                ? `<text class="s-lbl${visual.style === "native" ? " n" : ""}" x="${x + width / 2}" y="${y + height / 2 + .4}" font-size="${fontSize.toFixed(1)}">${visual.style === "native" ? "SPONSOR" : "ADV"}</text>`
                : "";
            return `<rect class="${slotClass}" x="${x}" y="${y}" width="${width}" height="${height}" rx="${corner}"/>${label}`;
        }).join("");

        return `<svg viewBox="${viewBoxes[format.dev]}" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Anteprima ${escapeHtml(format.name)}, ${devices[format.dev].label}">${base}<g class="slots">${scrim}${slots}</g></svg>`;
    }

    function initWidget(root) {
        const query = selector => root.querySelector(selector);
        const radioName = (root.dataset.radioName || "Kiss Kiss Napoli").trim();
        let formats;
        try {
            formats = JSON.parse(root.dataset.formats);
        } catch (error) {
            console.error("Widget Banner: impossibile leggere i dati dei posizionamenti.", error);
            return;
        }
        if (formats && !Array.isArray(formats) && typeof formats === "object") {
            formats = Object.values(formats);
        }
        if (!Array.isArray(formats)) {
            console.error("Widget Banner: i dati dei posizionamenti non hanno il formato previsto.");
            return;
        }
        const state = {device: "desktop", category: "all", search: ""};
        const devicesEl = query(".devices");
        const filtersEl = query(".filters");
        const gridEl = query(".grid");
        const titleEl = query(".section-title");
        const subtitleEl = query(".section-subtitle");
        const listEl = query(".price-list");
        const dialog = query(".format-dialog");
        const deviceName = query(".dialog-device");
        const dialogTitle = query(".dialog-title");
        const stage = query(".demo-stage");
        const demoNote = query(".demo-note");
        const sheet = query(".sheet");
        const demoPanel = query(".panel-demo");
        const sheetPanel = query(".panel-sheet");
        const demoTab = query(".tab-demo");
        const sheetTab = query(".tab-sheet");

        function renderDevices() {
            devicesEl.innerHTML = Object.entries(devices).map(([key, device]) =>
                `<button type="button" role="tab" aria-selected="${state.device === key}" data-device="${key}">${icons[key]}${device.label}<span class="n">${formats.filter(format => format.dev === key).length}</span></button>`
            ).join("");
        }

        function renderFilters() {
            const currentFormats = formats.filter(format => format.dev === state.device);
            const filters = [["all", "Tutti", currentFormats.length], ...Object.entries(categories).map(([key, label]) => [
                key,
                label,
                currentFormats.filter(format => format.cat === key).length
            ])];

            filtersEl.innerHTML = filters.map(([key, label, count]) =>
                `<button class="chip" type="button" aria-pressed="${state.category === key}" data-category="${key}" ${count === 0 && key !== "all" ? "disabled" : ""}>${label}<span class="n">${count}</span></button>`
            ).join("") + `<input class="search" type="search" placeholder="Cerca formato o misura…" aria-label="Cerca formato" value="${escapeHtml(state.search)}">`;
        }

        function renderGrid() {
            const device = devices[state.device];
            titleEl.innerHTML = device.title;
            subtitleEl.textContent = `Sito ${radioName} ${device.context}`;

            const search = state.search.trim().toLocaleLowerCase("it");
            const visibleFormats = formats.filter(format =>
                format.dev === state.device
                && (state.category === "all" || format.cat === state.category)
                && (!search || `${format.name} ${format.size} ${format.place}`.toLocaleLowerCase("it").includes(search))
            );

            gridEl.innerHTML = visibleFormats.length
                ? visibleFormats.map(format => {
                    const price = format.price === null
                        ? '<span class="val empty">€ —</span>'
                        : `<span class="val">${escapeHtml(formatPrice(format.price))}</span>`;
                    return `
                        <article class="card">
                            <div class="pv">${formatSvg(format)}</div>
                            <div class="card-top"><h3>${escapeHtml(format.name)}</h3><span class="tag">${escapeHtml(categories[format.cat] || format.cat)}</span></div>
                            <dl class="spec">
                                <div><dt>Posizionamento</dt><dd>${escapeHtml(format.place)}</dd></div>
                                <div><dt>Comportamento</dt><dd>${escapeHtml(format.fx)}</dd></div>
                                <div><dt>Dimensioni</dt><dd class="mono">${escapeHtml(format.dims)}</dd></div>
                            </dl>
                            <div class="price"><div><div class="model">${escapeHtml(format.model)}</div>${price}</div></div>
                            <div class="actions"><button class="btn" type="button" data-demo="${format.id}">Vedi demo</button><button class="link" type="button" data-sheet="${format.id}">Scheda tecnica</button></div>
                        </article>`;
                }).join("")
                : '<p class="empty-state">Nessun formato corrisponde alla ricerca.</p>';
        }

        function renderPriceList() {
            let html = "<thead><tr><th>Formato</th><th>Categoria</th><th>Dimensioni</th><th>Modello</th><th>Prezzo</th></tr></thead><tbody>";
            Object.entries(devices).forEach(([key, device]) => {
                html += `<tr><td colspan="5" class="dev-label">${device.label}</td></tr>`;
                html += formats.filter(format => format.dev === key).map(format =>
                    `<tr><td><strong>${escapeHtml(format.name)}</strong></td><td>${escapeHtml(categories[format.cat] || format.cat)}</td><td class="num">${escapeHtml(format.size)}</td><td>${escapeHtml(format.model)}</td><td class="num">${format.price === null ? '<span class="pill">Da definire</span>' : escapeHtml(formatPrice(format.price))}</td></tr>`
                ).join("");
            });
            listEl.innerHTML = html + "</tbody>";
        }

        function setDialogTab(tab) {
            const showDemo = tab === "demo";
            demoTab.setAttribute("aria-selected", String(showDemo));
            sheetTab.setAttribute("aria-selected", String(!showDemo));
            demoPanel.hidden = !showDemo;
            sheetPanel.hidden = showDemo;
        }

        function openDialog(id, tab) {
            const format = formats.find(item => item.id === id);
            if (!format) {
                return;
            }

            deviceName.textContent = `${devices[format.dev].label} · ${categories[format.cat]}`;
            dialogTitle.textContent = format.name;
            stage.innerHTML = formatSvg(format);
            stage.className = `demo-stage anim anim-${(formatVisuals[format.id] || {}).anim || "pulse"}`;
            demoNote.textContent = `${format.place}. ${format.fx}.`;

            const row = (label, value) => `<tr><th>${label}</th><td>${value || '<span class="pill">Da definire</span>'}</td></tr>`;
            sheet.innerHTML = row("Dispositivo", escapeHtml(devices[format.dev].label))
                + row("Categoria", escapeHtml(categories[format.cat] || format.cat))
                + row("Posizionamento", escapeHtml(format.place))
                + row("Comportamento", escapeHtml(format.fx))
                + row("Dimensioni", `<span class="mono">${escapeHtml(format.dims)}</span>`)
                + row("Formati file accettati", escapeHtml(format.file_formats))
                + row("Peso massimo", escapeHtml(format.max_weight))
                + row("Tracciamento terze parti", escapeHtml(format.third_party_tracking))
                + row("Consegna creatività", escapeHtml(format.creative_delivery))
                + row("Modello di prezzo", escapeHtml(format.model))
                + row("Prezzo", format.price === null ? '<span class="pill">Da definire</span>' : escapeHtml(formatPrice(format.price)));

            setDialogTab(tab);
            if (typeof dialog.showModal === "function") {
                dialog.showModal();
            } else {
                dialog.setAttribute("open", "");
            }
        }

        root.addEventListener("input", event => {
            if (event.target.matches(".search")) {
                state.search = event.target.value;
                renderGrid();
            }
        });

        root.addEventListener("click", event => {
            const button = event.target.closest("button");
            if (!button || !root.contains(button)) {
                return;
            }

            if (button.dataset.device) {
                state.device = button.dataset.device;
                state.category = "all";
                state.search = "";
                renderDevices();
                renderFilters();
                renderGrid();
            } else if (button.dataset.category) {
                state.category = button.dataset.category;
                renderFilters();
                renderGrid();
            } else if (button.dataset.demo) {
                openDialog(button.dataset.demo, "demo");
            } else if (button.dataset.sheet) {
                openDialog(button.dataset.sheet, "sheet");
            } else if (button.classList.contains("x")) {
                dialog.close();
            } else if (button.classList.contains("tab-demo")) {
                setDialogTab("demo");
            } else if (button.classList.contains("tab-sheet")) {
                setDialogTab("sheet");
            }
        });

        dialog.addEventListener("click", event => {
            if (event.target === dialog) {
                dialog.close();
            }
        });

        renderDevices();
        renderFilters();
        renderGrid();
        renderPriceList();
    }

    function boot() {
        document.querySelectorAll("[data-widget-banner]").forEach(initWidget);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot, {once: true});
    } else {
        boot();
    }
})();
