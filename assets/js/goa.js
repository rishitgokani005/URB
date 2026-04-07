document.addEventListener('DOMContentLoaded', () => {
    const agencies = [
      {
        name: 'Goa Coastal Cruisers',
        facilities: 'Availability: Yamaha R15, Royal Enfield',
        priceRange: 'Price:1500',
        link: 'goa-bikes.php',
        offer: 'Book Now'
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
