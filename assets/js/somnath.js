document.addEventListener('DOMContentLoaded', () => {
    const agencies = [
      {
        name: 'Shreeji Bike Rentals',
        facilities: 'Availability: Activa 6g, Access 125',
        priceRange: 'Price:500-600',
        link: 'shreejibike.php',
        offer: 'Book Now'
      },
      {
        name: 'Somnath  Bike Rentals',
        facilities: 'Availability: Activa 6g, Access 125',
        priceRange: 'Price:499-699',
        link: 'somnathbike.php',
        offer: 'Book Now'
      },
      {
        name: 'Ride & Rol Bike Rentals',
        facilities: 'Availability: Activa 6g, Access 125',
        priceRange: 'Price:499',
        link: 'samay.php',
        offer: 'Book Now'
      },

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
  