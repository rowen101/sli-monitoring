<script setup>
import { computed } from 'vue'

// Define props
const props = defineProps({
  name: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'text',
    validator: value => ['text', 'number', 'password', 'email', 'url', 'tel'].includes(value)
  },
  label: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: ''
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  errors: {
    type: [String, Array], // Allow array or string
    default: null
  }
})

// Define emit for two-way binding
const emit = defineEmits(['update:modelValue'])

// Computed value for v-model
const computedValue = computed({
  get: () => props.modelValue,
  set: value => {
    emit('update:modelValue', value)
  }
})
</script>

<template>
  <div class="form-group row">
    <!-- Label -->
    <label :for="name" v-if="label" class="col-sm-2 col-form-label">{{ label }}</label>

    <!-- Textfield Input -->
    <div class="col-sm-10">
      <input
        v-model="computedValue"
        :type="type"
        :id="name"
        :name="name"
        :placeholder="placeholder"
        :class="['form-control', errors ? 'is-invalid' : '']"
      >
      
      <!-- Error Feedback -->
      <span v-if="errors" class="invalid-feedback">
        <!-- Handle String or Array Errors -->
        <template v-if="Array.isArray(errors)">
          <ul>
            <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
          </ul>
        </template>
        <template v-else>{{ errors }}</template>
      </span>
    </div>
  </div>
</template>
