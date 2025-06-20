<template>
  <div class="property-search">
    <div class="container mt-4">
      <h1 class="mb-4">Property Search</h1>
      
      <!-- Search Form -->
      <div class="filter-section">
        <form @submit.prevent="performSearch" class="row g-3">
          <div class="col-md-6">
            <input 
              type="text" 
              class="form-control" 
              v-model="searchParams.q" 
              placeholder="Search area, city or description"
            >
          </div>
          <div class="col-md-3">
            <input 
              type="date" 
              class="form-control" 
              v-model="searchParams.start_date" 
              :min="minDate"
            >
          </div>
          <div class="col-md-3">
            <input 
              type="date" 
              class="form-control" 
              v-model="searchParams.end_date" 
              :min="searchParams.start_date || minDate"
            >
          </div>
          
          <div class="col-12">
            <button type="submit" class="btn btn-primary me-2">Search</button>
            <button type="button" class="btn btn-secondary" @click="resetForm">Reset</button>
          </div>
        </form>
      </div>

      <!-- Loading Indicator -->
      <div v-if="loading" class="loading">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Results Section -->
      <div class="row">
        <!-- Facets -->
        <div class="col-md-3">
          <div class="facet-section">
            <h4>Filters</h4>
            
            <!-- Rate Ranges Section -->
            <div class="facet-item" v-if="facets.rate_ranges">
              <h6>Price Range</h6>
              <ul class="list-unstyled">
                <li v-for="bucket in facets.rate_ranges.buckets" :key="bucket.key">
                  <div class="form-check">
                    <input 
                      class="form-check-input" 
                      type="checkbox" 
                      :id="`rate_ranges-${bucket.key}`"
                      :value="bucket.key"
                      v-model="selectedFacets.rate_ranges"
                      @change="updateFacetFilters"
                    >
                    <label class="form-check-label" :for="`rate_ranges-${bucket.key}`">
                      {{ formatRateRangeLabel(bucket.key) }} ({{ bucket.doc_count }})
                    </label>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Other Facets -->
            <div class="facet-item" v-for="(facet, name) in filteredFacets" :key="name">          

                <h6>{{ formatSnakeCase(name) }}</h6>
                <ul class="list-unstyled">
                    <li v-for="bucket in facet.buckets" :key="bucket.key">
                        <div class="form-check">
                            <input 
                            class="form-check-input" 
                            type="checkbox" 
                            :id="`${name}-${bucket.key}`"
                            :value="bucket.key"
                            v-model="selectedFacets[name]"
                            @change="updateFacetFilters"
                            >
                            <label class="form-check-label" :for="`${name}-${bucket.key}`">
                            {{ formatSnakeCase(bucket.key) }} ({{ bucket.doc_count }})
                            </label>
                        </div>
                    </li>
                </ul>

            </div>
          </div>
        </div>
        
        <!-- Property Results -->
        <div class="col-md-9">
          <div v-if="results.length === 0" class="text-center">
            No properties found
          </div>
          <div v-else class="row">
            <div v-for="property in results" 
                 :key="property._id" 
                 class="col-md-6 col-lg-4 mb-4">
              <PropertyCard :property="property._source" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import PropertyCard from './PropertyCard.vue'

export default {
  name: 'PropertySearch',
  components: {
    PropertyCard
  },
  setup() {
    const loading = ref(false)
    const results = ref([])
    const facets = ref({})
    const selectedFacets = ref({
      apartment_type: [],
      building_type: [],
      cities: [],
      rate_ranges: [],
      apartment_facilities: [],
      kitchen_facilities: [],
      building_facilities: [],
      health_and_safety_facilities: [],
      sustainability: []
    })
    const searchParams = ref({
      q: '',
      start_date: '',
      end_date: '',
      filters: {
        cities: '',
        rate_ranges: '',
        apartment_facilities: '',
        kitchen_facilities: '',
        building_facilities: '',
        health_and_safety_facilities: '',
        sustainability: ''
      }
    })

    const minDate = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    const filteredFacets = computed(() => {
      const filtered = {}
      Object.entries(facets.value).forEach(([key, value]) => {
        if (key !== 'rate_ranges') {
            filtered[key] = value
        }
      })
      return filtered
    })

    const formatSnakeCase = (text) => {
      if (!text) return '';
      
      if (Array.isArray(text)) {
        text = text[0];
      }
      
      if (typeof text !== 'string') return String(text);
            const words = text.split('_');
            const formatted = words.map(word => {
        if (!word) return '';
        return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
      });
      return formatted.join(' ');
    }

    const formatRateRange = (range) => {
      if (!range) return '';
      if (range.to && range.from) {
        return `£${range.from} - £${range.to}`;
      } else if (range.to) {
        return `Up to £${range.to}`;
      } else if (range.from) {
        return `£${range.from} and above`;
      }
      return '';
    }

    const formatRateRangeLabel = (range) => {
      if (!range) return '';
      if (range === '*-100.0') {
        return 'Up to £100';
      } else if (range === '100.0-200.0') {
        return '£100 - £200';
      } else if (range === '200.0-*') {
        return '£200 and above';
      }
      return range;
    }

    const performSearch = async () => {
      loading.value = true
      try {
        const params = new URLSearchParams()
        
        Object.entries(searchParams.value).forEach(([key, value]) => {
          if (value && key !== 'filters') {
            params.append(key, value)
          }
        })

        if (selectedFacets.value.rate_ranges.length > 0) {
          const rateRange = selectedFacets.value.rate_ranges[0] 

          if (rateRange === '*-100.0') {
            params.append('rate_max', '100')
          } else if (rateRange === '100.0-200.0') {
            params.append('rate_min', '100')
            params.append('rate_max', '200')
          } else if (rateRange === '200.0-*') {
            params.append('rate_min', '200')
          }
        }

        Object.entries(selectedFacets.value).forEach(([facet, values]) => {
          if (facet !== 'rate_ranges' && values.length > 0) {
            const fieldMapping = {
              'cities': 'city.raw',
              'apartment_type': 'apartment_type',
              'building_type': 'building_type',
              'apartment_facilities': 'apartment_facilities',
              'kitchen_facilities': 'kitchen_facilities',
              'building_facilities': 'building_facilities',
              'health_and_safety_facilities': 'health_and_safety_facilities',
              'sustainability': 'sustainability'
            }
            
            const actualField = fieldMapping[facet] ?? facet
            params.append(`filters[${actualField}]`, values.join(','))
          }
        })

        console.log('Search params:', params.toString())
        const response = await fetch(`/api/properties/search?${params.toString()}`)
        const data = await response.json()
    
        results.value = data.results
        facets.value = data.facets
      } catch (error) {
        console.error('Error searching properties:', error)
      } finally {
        loading.value = false
      }
    }

    const resetForm = () => {
      searchParams.value = {
        q: '',
        start_date: '',
        end_date: '',
        filters: {
          cities: '',
          rate_ranges: '',
          apartment_facilities: '',
          kitchen_facilities: '',
          building_facilities: '',
          health_and_safety_facilities: '',
          sustainability: ''
        }
      }
      selectedFacets.value = {
        apartment_type: [],
        building_type: [],
        cities: [],
        rate_ranges: [],
        apartment_facilities: [],
        kitchen_facilities: [],
        building_facilities: [],
        health_and_safety_facilities: [],
        sustainability: []
      }
      performSearch()
    }

    const updateFacetFilters = () => {
      performSearch()
    }

    onMounted(() => {
      performSearch()
    })

    return {
      loading,
      results,
      facets,
      selectedFacets,
      searchParams,
      minDate,
      performSearch,
      resetForm,
      updateFacetFilters,
      formatSnakeCase,
      formatRateRange,
      formatRateRangeLabel,
      filteredFacets
    }
  }
}
</script>

<style scoped>
.filter-section {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.loading {
  text-align: center;
  padding: 20px;
}

.facet-section {
  margin-top: 20px;
}

.facet-item {
  margin-bottom: 20px;
}
</style> 