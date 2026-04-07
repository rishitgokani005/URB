document.addEventListener('DOMContentLoaded', () => {
  const agencies = [
    {
      name: 'Sky Travels',
      facilities: 'Flight Booking, Hotel Stay, Guided Tours',
      priceRange: '$300 - $1200',
      link: 'sky-travels.html',
      offer: 'Special Deal'
    },
    {
      name: 'Wanderlust Ventures',
      facilities: 'Adventure Trips, Group Discounts, Airport Pickup',
      priceRange: '$500 - $1500',
      link: 'wanderlust-ventures.html',
      offer: 'Special Deal'
    },
    {
      name: 'Explore World',
      facilities: 'Family Packages, Luxury Stay, Cruise Booking',
      priceRange: '$700 - $2000',
      link: 'explore-world.html',
      offer: 'Special Deal'
    },
    {
      name: 'Globetrotters',
      facilities: 'Solo Traveler Packages, Local Guides, City Tours',
      priceRange: '$400 - $1100',
      link: 'globetrotters.html',
      offer: 'Special Deal'
    }
  ];

  const container = document.getElementById('agency-container');

  agencies.forEach(agency => {
    const card = document.createElement('div');
    card.classList.add('agency-card');

    card.innerHTML = `
      <a href="${agency.link}">
        <h2 class="agency-name">${agency.name}</h2>
        <p class="facilities">${agency.facilities}</p>
        <p class="price-range">${agency.priceRange}</p>
        <span class="badge">${agency.offer}</span>
      </a>
    `;

    container.appendChild(card);
  });
});
