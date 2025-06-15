<template>
  <div class="price-calculator">
    <div v-for="(group, groupKey) in calc.table" :key="groupKey" class="calc-group">
      <h3 class="group-title">{{ capitalize(groupKey) }}</h3>

      <div v-for="(item, index) in group" :key="index" class="calc-item">
        <div class="label-row">
          <span class="label">
            <svg class="question-icon" viewBox="0 0 20 20">
              <circle cx="10" cy="10" r="10" fill="#666"/>
              <text x="6" y="15" fill="#fff" font-size="12">?</text>
            </svg>
            {{ item.name }}
          </span>
          <span class="value">{{ item.value }}</span>
        </div>

        <div class="slider-wrapper">
          <input
            class="slider"
            type="range"
            :min="0"
            :max="item.max ?? (item.labels?.length - 1 ?? 5)"
            v-model="item.value"
          />
          <div class="slider-points">
            <span
              v-for="i in (item.max ?? (item.labels?.length - 1 ?? 5)) + 1"
              :key="i"
              class="dot"
              :class="{ active: item.value === i - 1 }"
            >
              <span class="dot-inner"/>
              <span class="dot-label" v-if="!item.labels">{{ i - 1 }}</span>
            </span>
          </div>
        </div>

        <div class="slider-labels" v-if="item.labels">
          <span v-for="(label, i) in item.labels" :key="i" class="slider-label">{{ label }}</span>
        </div>
      </div>
    </div>

    <div class="total-row">
      <span class="total-label">Total price:</span>
      <span class="total-value">{{ total.toFixed(2) }} $</span>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'

const calc = reactive(window.calcData || {})
const coeff = reactive(window.coefficientData || {})

const total = computed(() => {
  let result = 0
  for (const groupKey in calc.table) {
    const items = calc.table[groupKey]
    for (const item of items) {
      const coef = coeff[groupKey]?.[item.name] ?? 1
      result += item.value * coef
    }
  }
  return result
})

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}
</script>

<style scoped>
.price-calculator {
  max-width: 880px;
  margin: 30px auto;
  padding: 0 10px;
  color: #fff;
  font-family: "Helvetica Neue", sans-serif;
}

.group-title {
  font-size: 24px;
  font-weight: 400;
  margin: 40px 0 20px;
}

.calc-item {
  margin-bottom: 40px;
}

.label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 15px;
}

.label {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #aaa;
}

.question-icon {
  width: 16px;
  height: 16px;
}

.slider-wrapper {
  position: relative;
  height: 46px; /* чуть больше для размещения точек и круга */
}

.slider {
  width: 100%;
  appearance: none;
  height: 2px;
  background: #888;
  border-radius: 2px;
  outline: none;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;
}

/* Кнопка ползунка (центральная) */
.slider::-webkit-slider-thumb {
  appearance: none;
  width: 26px;
  height: 26px;
  background: transparent;
  border: 2px solid #fff;
  border-radius: 50%;
  box-shadow: 0 0 0 2px #000;
  cursor: pointer;
  position: relative;
  z-index: 10;
  transform: translateX(-8%);
  margin-top: 0px; /* центрирует круг по линии */
}

.slider::-moz-range-thumb {
  width: 26px;
  height: 26px;
  background: #000;
  border: 2px solid #fff;
  border-radius: 50%;
  box-shadow: 0 0 0 2px #000;
  cursor: pointer;
}

.slider::-webkit-slider-thumb:active {
  background: #000;
}

/* Точки на линии */
.slider-points {
  display: flex;
  justify-content: space-between;
  position: absolute;
  top: 26%;
  transform: translateY(-50%);
  width: 100%;
  height: 0;
  z-index: 1;
  pointer-events: none;
}

.dot {
  position: relative;
  width: 26px;
  height: 26px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.dot-inner {
  width: 11px;
  height: 11px;
  background: #aaa;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.dot.active .dot-inner {
  background: #fff;
  width: 10px;
  height: 10px;
}

.dot-label {
  position: absolute;
  top: 28px;
  font-size: 13px;
  color: #bbb;
  font-weight: 400;
}

/* Labels (если указаны) */
.slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
  font-size: 13px;
  color: #aaa;
  font-weight: 400;
}

.slider-label {
  text-align: center;
  flex: 1;
}

.total-row {
  display: flex;
  justify-content: space-between;
  font-size: 17px;
  font-weight: 500;
  padding-top: 30px;
  border-top: 1px solid #444;
  margin-top: 50px;
}

.total-label {
  color: #aaa;
}
.total-value {
  color: #0af;
}
</style>




