$(function () {
  const photoTitles = [
    'Golden Harbor',
    'Quiet Morning',
    'Blue Horizon',
    'City Reflection',
    'Wildflower Path',
    'Sunlit Shore',
    'Open Fields',
    'Harbor Lights',
    'Forest Trail',
    'Night Bloom',
    'Coastal Walk',
    'Warm Sunset',
    'Morning Avenue',
    'River Bend',
    'Silver Peaks',
    'Soft Waves',
    'Garden Light',
    'Cloud Drift',
    'Stone Bridge',
    'Lakeside Calm',
    'Amber Glow',
    'Rainy Street',
    'Moonrise Bay',
    'Crimson Sky'
  ];

  const state = {
    items: [],
    filteredItems: [],
    controller: null,
    searchTerm: ''
  };

  const resultsGrid = $('#resultsGrid');
  const loadingState = $('#loadingState');
  const emptyState = $('#emptyState');
  const alertArea = $('#alertArea');
  const searchInput = $('#searchInput');
  const clearSearch = $('#clearSearch');
  const resultCount = $('#resultCount');
  const currentLabel = $('#currentLabel');
  const currentEndpoint = $('#currentEndpoint');

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function buildSearchIndex(item) {
    return [item.title, item.subtitle, item.meta, item.album].filter(Boolean).join(' ').toLowerCase();
  }

  function escapeXml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&apos;');
  }

  function createPreviewImage(title, index) {
    const palette = [
      ['#2df3df', '#091a33'],
      ['#67a8ff', '#10213d'],
      ['#ffb36b', '#2b1739'],
      ['#90f08f', '#112d24']
    ];
    const [accentA, accentB] = palette[index % palette.length];
    const safeTitle = escapeXml(title);
    const svg = `
      <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="840" viewBox="0 0 1200 840">
        <defs>
          <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="${accentA}"/>
            <stop offset="100%" stop-color="${accentB}"/>
          </linearGradient>
          <linearGradient id="shine" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="rgba(255,255,255,0.34)"/>
            <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
          </linearGradient>
        </defs>
        <rect width="1200" height="840" fill="url(#bg)"/>
        <circle cx="940" cy="150" r="200" fill="rgba(255,255,255,0.12)"/>
        <circle cx="190" cy="670" r="240" fill="rgba(255,255,255,0.08)"/>
        <rect x="90" y="90" width="1020" height="660" rx="56" fill="rgba(4,8,20,0.22)" stroke="rgba(255,255,255,0.28)" stroke-width="4"/>
        <path d="M180 588 L410 372 L560 520 L720 310 L1020 588 Z" fill="rgba(255,255,255,0.18)"/>
        <circle cx="368" cy="278" r="74" fill="rgba(255,255,255,0.9)"/>
        <text x="130" y="165" fill="rgba(255,255,255,0.88)" font-family="Poppins, Arial, sans-serif" font-size="38" font-weight="700" letter-spacing="4">PHOTO</text>
        <text x="130" y="360" fill="#ffffff" font-family="Poppins, Arial, sans-serif" font-size="74" font-weight="800">${safeTitle}</text>
        <text x="130" y="440" fill="rgba(255,255,255,0.9)" font-family="Poppins, Arial, sans-serif" font-size="30" font-weight="500">Gallery Preview ${index + 1}</text>
        <rect x="130" y="520" width="240" height="6" rx="3" fill="url(#shine)"/>
      </svg>
    `;

    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
  }

  function setLoading(isLoading) {
    loadingState.toggleClass('d-none', !isLoading);
  }

  function showAlert(message) {
    alertArea.html(`
      <div class="alert alert-danger border-0 rounded-4 mb-4" role="alert">
        ${escapeHtml(message)}
      </div>
    `);
  }

  function clearAlert() {
    alertArea.empty();
  }

  function updateHeader() {
    currentLabel.text('Photos');
    currentEndpoint.text('JSONPlaceholder photo feed');
  }

  function normalizePhotos(data) {
    return data.slice(0, 24).map((item, index) => ({
      title: photoTitles[index] || `Photo ${index + 1}`,
      subtitle: `Album ${item.albumId}`,
      meta: `Photo ID ${item.id}`,
      image: createPreviewImage(photoTitles[index] || `Photo ${index + 1}`, index),
      searchText: buildSearchIndex({
        title: photoTitles[index] || `Photo ${index + 1}`,
        subtitle: `Album ${item.albumId}`,
        meta: `Photo ID ${item.id}`,
        album: item.albumId
      })
    }));
  }

  function renderItems(items) {
    resultsGrid.html(items.map((item) => `
      <div class="col">
        <article class="result-card card border-0 overflow-hidden h-100">
          <img class="result-thumb" src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}">
          <div class="card-body d-flex flex-column gap-2">
            <span class="badge rounded-pill badge-soft align-self-start">${escapeHtml(item.subtitle)}</span>
            <h3 class="result-title fw-semibold mb-0">${escapeHtml(item.title)}</h3>
            <p class="result-copy mb-0">${escapeHtml(item.meta)}</p>
          </div>
        </article>
      </div>
    `).join(''));
  }

  function applySearch() {
    const term = state.searchTerm.trim().toLowerCase();
    state.filteredItems = term
      ? state.items.filter((item) => item.searchText.includes(term))
      : [...state.items];

    renderItems(state.filteredItems);
    resultCount.text(`${state.filteredItems.length} items`);
    emptyState.toggleClass('d-none', state.filteredItems.length !== 0);
  }

  function loadPhotos() {
    if (state.controller) {
      state.controller.abort();
    }

    state.searchTerm = '';
    searchInput.val('');
    clearAlert();
    resultsGrid.empty();
    emptyState.addClass('d-none');
    setLoading(true);

    state.controller = new AbortController();

    fetch('https://jsonplaceholder.typicode.com/photos?_limit=24', {
      signal: state.controller.signal
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Failed to load photos.');
        }
        return response.json();
      })
      .then((data) => {
        state.items = normalizePhotos(data);
        applySearch();
      })
      .catch((error) => {
        if (error.name !== 'AbortError') {
          showAlert(error.message || 'Something went wrong while fetching photos.');
          resultsGrid.empty();
          resultCount.text('0 items');
        }
      })
      .finally(() => {
        setLoading(false);
      });
  }

  searchInput.on('input', function () {
    state.searchTerm = $(this).val() || '';
    applySearch();
  });

  clearSearch.on('click', function () {
    state.searchTerm = '';
    searchInput.val('');
    applySearch();
    searchInput.trigger('focus');
  });

  updateHeader();
  loadPhotos();
});