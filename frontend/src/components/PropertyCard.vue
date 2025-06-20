<template>
  <div class="card property-card h-100" @click="showPropertyDetails">
    <div class="property-image-container">
      <img 
        :src="property.image_url || '/frontend/images/placeholder.jpg'" 
        class="card-img-top property-image" 
        :alt="property.name || 'Property image'"
        @error="handleImageError"
      >
    </div>
    <div class="card-body">
      <h5 class="card-title">{{ property.name || 'Unnamed Property' }}</h5>

      <h6>
          {{ property.area || '' }}, {{ property.city || 'Location not specified' }}
      </h6>
      <div class="property-details">
        <small class="text-muted">
          <i class="bi bi-building"></i> {{ formatSnakeCase(property.apartment_type) || 'N/A' }}
        </small>
        <small class="text-muted">
          <i class="bi bi-leaf text-success"></i> {{ property.emission || '0' }} kg CO₂e
        </small>
      </div>
      <div class="property-rate">
        <div class="rate mt-2">
          <small class="text-muted">From </small>
          <span class="text-primary fw-bold">£{{ property.rate || '0' }}</span>
          <small class="text-muted"> per night</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Property Details Modal -->
  <div v-if="showModal" class="modal-backdrop" @click="closeModal">
    <div v-if="showModal" class="modal show d-block" tabindex="-1">
        <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content" >
            <div class="modal-header">
            <h5 class="modal-title">{{ property.name }}</h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                <img 
                    :src="property.image_url || '/frontend/images/placeholder.jpg'" 
                    class="img-fluid rounded" 
                    :alt="property.name"
                    @error="handleImageError"
                >
                </div>
                <div class="col-md-6">                
                <div class="mb-3">
                  <p class="text-muted">{{ property.description }}</p>
                </div>
                <div class="mb-3">
                    <h5>Property Details</h5>
                    <ul class="list-unstyled">
                    <li><strong>Area:</strong> {{ property.area }}, {{ property.city }}</li>
                    <li><strong>Property ID:</strong> {{ property.property_id }}</li>
                    <li><strong>Apartment ID:</strong> {{ property.apartment_id }}</li>
                    <li><strong>Apartment Type:</strong> {{ formatSnakeCase(property.apartment_type) }}</li>
                    <li><strong>Building Type:</strong> {{ formatSnakeCase(property.building_type) }}</li>
                    <li><strong>Rate:</strong> £{{ property.rate }} per night</li>
                    <li><strong>Rating:</strong> {{ property.rating }}/5</li>
                    <li><strong>Available:</strong> {{ property.available ? 'Yes' : 'No' }}</li>
                    <li><strong>Emission:</strong> {{ property.emission }} kg CO₂e</li>
                    </ul>
                </div>

                <div class="mb-3" v-if="property.apartment_facilities && property.apartment_facilities.length">
                    <h6>Apartment Facilities</h6>
                    <div class="d-flex flex-wrap gap-1">
                    <span v-for="facility in property.apartment_facilities" :key="facility" class="badge bg-light text-dark">
                        {{ formatSnakeCase(facility) }}
                    </span>
                    </div>
                </div>

                <div class="mb-3" v-if="property.kitchen_facilities && property.kitchen_facilities.length">
                    <h6>Kitchen Facilities</h6>
                    <div class="d-flex flex-wrap gap-1">
                    <span v-for="facility in property.kitchen_facilities" :key="facility" class="badge bg-light text-dark">
                        {{ formatSnakeCase(facility) }}
                    </span>
                    </div>
                </div>

                <div class="mb-3" v-if="property.building_facilities && property.building_facilities.length">
                    <h6>Building Facilities</h6>
                    <div class="d-flex flex-wrap gap-1">
                    <span v-for="facility in property.building_facilities" :key="facility" class="badge bg-light text-dark">
                        {{ formatSnakeCase(facility) }}
                    </span>
                    </div>
                </div>

                <div class="mb-3" v-if="property.health_and_safety_facilities && property.health_and_safety_facilities.length">
                    <h6>Health & Safety Facilities</h6>
                    <div class="d-flex flex-wrap gap-1">
                    <span v-for="facility in property.health_and_safety_facilities" :key="facility" class="badge bg-light text-dark">
                        {{ formatSnakeCase(facility) }}
                    </span>
                    </div>
                </div>

                <div class="mb-3" v-if="property.sustainability && property.sustainability.length">
                    <h6>Sustainability Features</h6>
                    <div class="d-flex flex-wrap gap-1">
                    <span v-for="feature in property.sustainability" :key="feature" class="badge bg-success text-white">
                        {{ formatSnakeCase(feature) }}
                    </span>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
            <button type="button" class="btn btn-primary">Book Now</button>
            </div>
        </div>
        </div>
    </div>
</div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'PropertyCard',
  props: {
    property: {
      type: Object,
      required: true
    }
  },
  setup() {
    const showModal = ref(false)

    const showPropertyDetails = () => {
      showModal.value = true
      // Add ESC key listener
      document.addEventListener('keydown', handleEscKey)
    }

    const closeModal = () => {
      showModal.value = false
      // Remove ESC key listener
      document.removeEventListener('keydown', handleEscKey)
    }

    const handleEscKey = (event) => {
      if (event.key === 'Escape' && showModal.value) {
        closeModal()
      }
    }

    return {
      showModal,
      showPropertyDetails,
      closeModal
    }
  },
  methods: {
    handleImageError(e) {
      e.target.src = '/frontend/images/placeholder.jpg'
    },
    formatSnakeCase(text) {
      if (!text) return '';
      
      // Handle Proxy array case
      if (Array.isArray(text)) {
        text = text[0];
      }
      
      if (typeof text !== 'string') return String(text);
      
      // First split by underscores
      const words = text.split('_');
      
      // Then capitalize each word
      const formatted = words.map(word => {
        if (!word) return '';
        return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
      });
      
      // Join with spaces
      return formatted.join(' ');
    }
  }
}
</script>

<style scoped>
.property-card {
  transition: transform 0.2s;
  cursor: pointer;
}

.property-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.property-image-container {
  height: 200px;
  overflow: hidden;
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}

.property-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.property-card:hover .property-image {
  transform: scale(1.05);
}

.card-title {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.card-text {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 1rem;
}

.property-details {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.rate {
  font-size: 1.2rem;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

.modal-body img {
  width: 100%;
  max-width: 100%;
  height: 300px;
  max-height: 400px;
  object-fit: cover;
  display: block;
  margin: 0 auto;
}

.badge {
  font-size: 0.75rem;
}
</style> 