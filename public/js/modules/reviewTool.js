/**
 * reviewTool.js — Immobilien-Reviewtool Frontend Module
 *
 * Handles:
 *   - Tab switching (calculator / history)
 *   - PLZ autocomplete search
 *   - Calculation form submission (AJAX)
 *   - Save valuation (AJAX)
 *   - History list with search & pagination
 *   - View / Edit / Delete actions
 */

// ===================================================================
//  CSRF Token
// ===================================================================

/**
 * Get CSRF token from the hidden input in the form
 * @returns {string}
 */
function getCsrfToken() {
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    return tokenInput ? tokenInput.value : '';
}

// ===================================================================
//  DOM References
// ===================================================================

const reviewTabs = document.getElementById('reviewTabs');
const panelCalculator = document.getElementById('panel-calculator');
const panelHistory = document.getElementById('panel-history');
const plzInput = document.getElementById('plz');
const plzSuggestions = document.getElementById('plz-suggestions');
const plzHint = document.getElementById('plz-hint');
const valuationForm = document.getElementById('valuationForm');
const calcLoader = document.getElementById('calcLoader');
const valuationResult = document.getElementById('valuationResult');
const btnSave = document.getElementById('btnSave');
const btnReset = document.getElementById('btnReset');
const historySearch = document.getElementById('historySearch');
const historyList = document.getElementById('historyList');
const historyPagination = document.getElementById('historyPagination');
const prevPageBtn = document.getElementById('prevPage');
const nextPageBtn = document.getElementById('nextPage');
const pageInfo = document.getElementById('pageInfo');

// Result placeholders
const resultLocation = document.getElementById('resultLocation');
const resultPricePerSqm = document.getElementById('resultPricePerSqm');
const resultTotalValue = document.getElementById('resultTotalValue');
const resultRange = document.getElementById('resultRange');
const resultLocFactor = document.getElementById('resultLocFactor');
const resultCondFactor = document.getElementById('resultCondFactor');
const resultEquipFactor = document.getElementById('resultEquipFactor');
const resultResidenceFactor = document.getElementById('resultResidenceFactor');

// Save form hidden fields
const savePlz = document.getElementById('save_plz');
const saveLocationName = document.getElementById('save_location_name');
const savePropertyType = document.getElementById('save_property_type');
const saveArea = document.getElementById('save_area');
const saveCondition = document.getElementById('save_condition');
const saveEquipment = document.getElementById('save_equipment');
const savePricePerSqm = document.getElementById('save_price_per_sqm');
const saveTotalValue = document.getElementById('save_total_value');
const saveLocationFactor = document.getElementById('save_location_factor');
const saveConditionFactor = document.getElementById('save_condition_factor');
const saveEquipmentFactor = document.getElementById('save_equipment_factor');
const saveResidenceStatus = document.getElementById('save_residence_status');
const saveResidenceFactor = document.getElementById('save_residence_status_factor');

// ===================================================================
//  State
// ===================================================================

let currentCalcData = null;   // Store last calculation result
let historyPage = 1;
const historyPageSize = 10;
let historySearchTerm = '';
let historyTotal = 0;

// ===================================================================
//  Tab Switching
// ===================================================================

function initTabs() {
    if (!reviewTabs) return;

    const tabs = reviewTabs.querySelectorAll('.review-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Show corresponding panel
            if (target === 'calculator') {
                panelCalculator.classList.add('active');
                panelHistory.classList.remove('active');
            } else if (target === 'history') {
                panelHistory.classList.add('active');
                panelCalculator.classList.remove('active');
                loadHistory();  // Load history when switching to tab
            }
        });
    });
}

// ===================================================================
//  PLZ Autocomplete
// ===================================================================

let plzDebounceTimer = null;

function initPlzAutocomplete() {
    if (!plzInput) return;

    plzInput.addEventListener('input', () => {
        const query = plzInput.value.trim();
        console.log('PLZ input changed:', typeof query, query);
        // Clear debounce timer
        if (plzDebounceTimer) clearTimeout(plzDebounceTimer);

        if (query.length < 2) {
            hideSuggestions();
            return;
        }

        // Debounce API call
        plzDebounceTimer = setTimeout(() => {
            fetchPlzSuggestions(query);
        }, 250);
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!plzInput.contains(e.target) && !plzSuggestions.contains(e.target)) {
            hideSuggestions();
        }
    });

    // Close suggestions on escape
    plzInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            hideSuggestions();
        }
    });
}

async function fetchPlzSuggestions(query) {
  try {
    const csrf = getCsrfToken();
    console.log("CSRF token:", csrf ? csrf.substring(0, 8) + "..." : "EMPTY");
    if (!csrf) {
      console.error("CSRF token not found in DOM");
      hideSuggestions();
      return;
    }

    const body = JSON.stringify({ query: query, csrf_token: csrf });

    const response = await fetch("/homes/search", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrf,
      },
      body: JSON.stringify({
        query: query,
      }),
    });

      if (!response.ok) {
      const text = await response.text();
      console.error(
        "PLZ search HTTP error:",
        response.status,
        response.headers.get("content-type"),
        text.substring(0, 500),
      );
      hideSuggestions();
      return;
    }

    const contentType = response.headers.get("content-type") || "";
    if (!contentType.includes("application/json")) {
      const text = await response.text();
    //   console.error(
    //     "PLZ search non-JSON response:",
    //     contentType,
    //     text.substring(0, 500),
    //   );
      hideSuggestions();
      return;
    }

    const data = await response.json();
    //console.log("PLZ search result:", data);
    displaySuggestions(data);
  } catch (error) {
    // console.error("PLZ search error:", error);
    hideSuggestions();
  }
}

function displaySuggestions(locations) {
    if (!plzSuggestions) return;

    plzSuggestions.innerHTML = '';

    if (locations.length === 0) {
        hideSuggestions();
        return;
    }

    locations.forEach(loc => {
        const div = document.createElement('div');
        
        div.className = 'plz-suggestion';
        div.innerHTML = `<strong>${loc.plz}</strong> — ${loc.name} (${loc.country})`;
        div.addEventListener('click', () => {
            plzInput.value = loc.plz;
            if (plzHint) {
                plzHint.textContent = `${loc.name}, ${loc.country}`;
            }
            hideSuggestions();
        });
        plzSuggestions.appendChild(div);
    });

    plzSuggestions.style.display = 'block';
}

function hideSuggestions() {
    if (plzSuggestions) {
        plzSuggestions.style.display = 'none';
    }
}

// ===================================================================
//  Calculation
// ===================================================================

function initCalculation() {
    if (!valuationForm) return;

    valuationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        performCalculation();
    });

    if (btnReset) {
        btnReset.addEventListener('click', resetForm);
    }

    if (btnSave) {
        btnSave.addEventListener('click', saveValuation);
    }
}

async function performCalculation() {
    // Validate form
    const plz = plzInput?.value.trim();
    const propertyType = document.getElementById('property_type')?.value;
    const area = document.getElementById('area')?.value;
    const condition = document.getElementById('condition')?.value;
    const equipment = document.getElementById('equipment')?.value;
    const residenceStatus = document.getElementById('residence_status')?.value || 'erstwohnsitz';

    if (!plz || !propertyType || !area || !condition || !equipment) {
        showFlash('Bitte füllen Sie alle Felder aus.', 'warning');
        return;
    }

    // Show loader
    if (calcLoader) calcLoader.style.display = 'flex';
    if (valuationResult) valuationResult.style.display = 'none';

    try {
        const response = await fetch('/homes/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                plz: plz,
                property_type: propertyType,
                area: parseFloat(area),
                condition: condition,
                equipment: equipment,
                residence_status: residenceStatus,
                csrf_token: getCsrfToken(),
            }),
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            showFlash(data.error || 'Berechnung fehlgeschlagen.', 'warning');
            return;
        }

        currentCalcData = data.data;
        displayResult(currentCalcData);

    } catch (error) {
        console.error('Calculation error:', error);
        showFlash('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'warning');
    } finally {
        if (calcLoader) calcLoader.style.display = 'none';
    }
}

function displayResult(data) {
    if (!valuationResult) return;

    // Location
    if (resultLocation) {
        resultLocation.textContent = `${data.location_name} (${data.plz})`;
    }

    // Price per sqm
    if (resultPricePerSqm) {
        resultPricePerSqm.textContent = formatCurrency(data.price_per_sqm, 'CHF') + '/m²';
    }

    // Total value
    if (resultTotalValue) {
        resultTotalValue.textContent = formatCurrency(data.total_value, 'CHF');
    }

    // Range (−10% to +10%)
    if (resultRange) {
        const low = data.total_value * 0.9;
        const high = data.total_value * 1.1;
        resultRange.textContent = `${formatCurrency(low)} – ${formatCurrency(high)} CHF`;
    }

    // Factors
    if (resultLocFactor) resultLocFactor.textContent = data.location_factor.toFixed(3).replace('.', ',');
    if (resultCondFactor) resultCondFactor.textContent = data.condition_factor.toFixed(3).replace('.', ',');
    if (resultEquipFactor) resultEquipFactor.textContent = data.equipment_factor.toFixed(3).replace('.', ',');
    if (resultResidenceFactor) resultResidenceFactor.textContent = (data.residence_status_factor || 1.0).toFixed(3).replace('.', ',');

    // Show result
    valuationResult.style.display = 'block';

    // Populate save form hidden fields
    if (savePlz) savePlz.value = data.plz;
    if (saveLocationName) saveLocationName.value = data.location_name;
    if (savePropertyType) savePropertyType.value = data.property_type;
    if (saveArea) saveArea.value = data.area;
    if (saveCondition) saveCondition.value = data.condition;
    if (saveEquipment) saveEquipment.value = data.equipment;
    if (savePricePerSqm) savePricePerSqm.value = data.price_per_sqm;
    if (saveTotalValue) saveTotalValue.value = data.total_value;
    if (saveLocationFactor) saveLocationFactor.value = data.location_factor;
    if (saveConditionFactor) saveConditionFactor.value = data.condition_factor;
    if (saveEquipmentFactor) saveEquipmentFactor.value = data.equipment_factor;
    if (saveResidenceStatus) saveResidenceStatus.value = data.residence_status || 'erstwohnsitz';
    if (saveResidenceFactor) saveResidenceFactor.value = data.residence_status_factor || 1.0;

    // Scroll to result
    valuationResult.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForm() {
    if (valuationForm) valuationForm.reset();
    if (valuationResult) valuationResult.style.display = 'none';
    if (plzHint) plzHint.textContent = '';
    if (plzSuggestions) plzSuggestions.style.display = 'none';
    currentCalcData = null;

    // Scroll to top of form
    valuationForm?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ===================================================================
//  Save Valuation
// ===================================================================

async function saveValuation() {
    if (!currentCalcData) return;

    if (btnSave) btnSave.disabled = true;
    if (btnSave) btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Speichern…';
  
    console.log(currentCalcData, getCsrfToken());
    try {
        const response = await fetch('/homes/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ ...currentCalcData,  csrf_token: getCsrfToken()}),
        });

        let data;
        if (response.ok) {
            data = await response.json();
        } else {
            const text = await response.text();
            console.error('Save error:', response.status, response.statusText, text?.slice(0, 500));
            showFlash('Speichern fehlgeschlagen.', 'warning');
            return;
        }

        if (!data.success) {
            showFlash(data.error || 'Speichern fehlgeschlagen.', 'warning');
            return;
        }

        showFlash('Bewertung erfolgreich gespeichert!', 'success');

        // Switch to history tab to show saved valuation
        switchToHistoryTab();

    } catch (error) {
        console.error('Save error:', error);
        showFlash('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'warning');
    } finally {
        if (btnSave) {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="fas fa-save"></i> Bewertung speichern';
        }
    }
}

// ===================================================================
//  History List
// ===================================================================

function initHistory() {
    if (!historySearch) return;

    let searchDebounceTimer = null;

    historySearch.addEventListener('input', () => {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            historySearchTerm = historySearch.value.trim();
            historyPage = 1;
            loadHistory();
        }, 300);
    });

    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (historyPage > 1) {
                historyPage--;
                loadHistory();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            if (historyPage < Math.ceil(historyTotal / historyPageSize)) {
                historyPage++;
                loadHistory();
            }
        });
    }
}

async function loadHistory() {
    if (!historyList) return;

    historyList.innerHTML = '<p class="review-history__empty">Lade Bewertungen…</p>';

    const params = new URLSearchParams({
        search: historySearchTerm,
        page: historyPage,
        per_page: historyPageSize,
    });

    try {
        const response = await fetch(`/homes/list?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (!data.success) {
            historyList.innerHTML = '<p class="review-history__empty">Keine Bewertungen gefunden.</p>';
            return;
        }

        historyTotal = data.total;
        const valuations = data.valuations;

        if (!valuations || valuations.length === 0) {
            historyList.innerHTML = '<p class="review-history__empty">Keine Bewertungen gefunden.</p>';
            if (historyPagination) historyPagination.style.display = 'none';
            return;
        }

        renderHistoryCards(valuations);
        renderPagination();

    } catch (error) {
        console.error('History load error:', error);
        historyList.innerHTML = '<p class="review-history__empty">Fehler beim Laden. Bitte versuchen Sie es erneut.</p>';
    }
}

function renderHistoryCards(valuations) {
    if (!historyList) return;

    historyList.innerHTML = '';

    valuations.forEach(v => {
        const card = document.createElement('div');
        card.className = 'review-history__card';

        const formattedValue = formatCurrency(v.total_value, 'CHF');
        const formattedPrice = formatCurrency(v.price_per_sqm, 'CHF');
        const dateStr = v.created_at ? new Date(v.created_at).toLocaleDateString('de-CH') : '—';

        card.innerHTML = `
            <div class="review-history__card-head">
                <span class="review-history__badge">${v.plz}</span>
                <small>${dateStr}</small>
            </div>
            <div class="review-history__card-body">
                <span><i class="fas fa-map-marker-alt"></i> ${v.location_name || '—'}</span>
                <span><i class="fas fa-home"></i> ${v.property_type || '—'}</span>
                <span><i class="fas fa-ruler-combined"></i> ${v.area || '—'} m²</span>
                <span><i class="fas fa-chart-line"></i> ${formattedPrice}/m²</span>
                <span><i class="fas fa-coins"></i> <strong>${formattedValue}</strong></span>
            </div>
            <div class="review-history__card-actions">
                <button class="review-btn review-btn--sm btn-view" data-id="${v.id}" title="Anzeigen">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="review-btn review-btn--sm btn-edit" data-id="${v.id}" title="Bearbeiten">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="review-btn review-btn--sm btn-delete" data-id="${v.id}" title="Löschen">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

        // Attach event listeners
        const viewBtn = card.querySelector('.btn-view');
        const editBtn = card.querySelector('.btn-edit');
        const deleteBtn = card.querySelector('.btn-delete');

        viewBtn?.addEventListener('click', () => viewValuation(v.id));
        editBtn?.addEventListener('click', () => editValuation(v));
        deleteBtn?.addEventListener('click', () => deleteValuation(v.id));

        historyList.appendChild(card);
    });
}

function renderPagination() {
    if (!historyPagination || !pageInfo) return;

    const totalPages = Math.ceil(historyTotal / historyPageSize);

    if (totalPages <= 1) {
        historyPagination.style.display = 'none';
        return;
    }

    historyPagination.style.display = 'flex';
    pageInfo.textContent = `Seite ${historyPage} von ${totalPages}`;

    prevPageBtn.disabled = historyPage <= 1;
    nextPageBtn.disabled = historyPage >= totalPages;
}

// ===================================================================
//  View / Edit / Delete Actions
// ===================================================================

async function viewValuation(id) {
    try {
        const response = await fetch(`/homes/show/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (!data.success || !data.data) {
            showFlash('Bewertung nicht gefunden.', 'warning');
            return;
        }

        currentCalcData = data.data;
        displayResult(currentCalcData);

        // Switch to calculator tab
        switchToCalculatorTab();

    } catch (error) {
        console.error('View error:', error);
        showFlash('Fehler beim Laden der Bewertung.', 'warning');
    }
}

async function editValuation(valuation) {
    currentCalcData = valuation;

    // Switch to calculator tab and populate form
    switchToCalculatorTab();

    if (plzInput) plzInput.value = valuation.plz || '';
    if (plzHint) plzHint.textContent = valuation.location_name || '';

    const propertyTypeSelect = document.getElementById('property_type');
    if (propertyTypeSelect) propertyTypeSelect.value = valuation.property_type || '';

    const areaInput = document.getElementById('area');
    if (areaInput) areaInput.value = valuation.area || '';

    const conditionSelect = document.getElementById('condition');
    if (conditionSelect) conditionSelect.value = valuation.condition || '';

    const equipmentSelect = document.getElementById('equipment');
    if (equipmentSelect) equipmentSelect.value = valuation.equipment || '';

    // Display the result
    if (valuation.price_per_sqm && valuation.total_value) {
        displayResult(valuation);
    }

    // Scroll to form
    valuationForm?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function deleteValuation(id) {
    if (!confirm('Möchten Sie diese Bewertung wirklich löschen?')) {
        return;
    }

    try {
        const response = await fetch(`/homes/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (!data.success) {
            showFlash(data.error || 'Löschen fehlgeschlagen.', 'warning');
            return;
        }

        showFlash('Bewertung gelöscht.', 'success');

        // Reload history
        loadHistory();

    } catch (error) {
        console.error('Delete error:', error);
        showFlash('Fehler beim Löschen.', 'warning');
    }
}

// ===================================================================
//  Helpers
// ===================================================================

function switchToHistoryTab() {
    if (!reviewTabs) return;
    const tabs = reviewTabs.querySelectorAll('.review-tab');
    tabs.forEach(t => t.classList.remove('active'));
    tabs[1]?.classList.add('active');
    panelHistory.classList.add('active');
    panelCalculator.classList.remove('active');
    loadHistory();
}

function switchToCalculatorTab() {
    if (!reviewTabs) return;
    const tabs = reviewTabs.querySelectorAll('.review-tab');
    tabs.forEach(t => t.classList.remove('active'));
    tabs[0]?.classList.add('active');
    panelCalculator.classList.add('active');
    panelHistory.classList.remove('active');
}

function formatCurrency(value, currency = 'CHF') {
    if (value === null || value === undefined) return '—';
    return new Intl.NumberFormat('de-CH', {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value);
}

function showFlash(text, type = 'info') {
    // Check if flash container exists, if not create one
    let flashContainer = document.querySelector('.review-flash-container');
    if (!flashContainer) {
        flashContainer = document.createElement('div');
        flashContainer.className = 'review-flash-container';
        const main = document.querySelector('.review-main__wrapper');
        if (main) {
            main.insertBefore(flashContainer, main.firstChild);
        }
    }

    const iconMap = {
        success: 'fa-check-circle',
        warning: 'fa-exclamation-circle',
        error: 'fa-times-circle',
        info: 'fa-info-circle',
    };

    const flash = document.createElement('div');
    flash.className = `review-flash review-flash--${type}`;
    flash.innerHTML = `<i class="fas ${iconMap[type] || iconMap.info}"></i> ${text}`;

    flashContainer.appendChild(flash);

    // Auto-remove after 4 seconds
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transition = 'opacity 0.3s';
        setTimeout(() => flash.remove(), 300);
    }, 4000);
}

// ===================================================================
//  Public API
// ===================================================================

export function init() {
    initTabs();
    initPlzAutocomplete();
    initCalculation();
    initHistory();

    // If there's a session-stored result, display it
    const storedResult = document.getElementById('_calc_result');
    // The Twig template handles this; we just ensure the result panel is visible
    if (valuationResult && valuationResult.style.display === 'block') {
        valuationResult.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
