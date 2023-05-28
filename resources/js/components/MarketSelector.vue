<template>
  <dropdown class="ml-8 h-9 flex items-center dropdown-right">
    <dropdown-trigger class="h-9 flex items-center">
      <span class="text-90">{{ nameToShow }}</span>
    </dropdown-trigger>

    <dropdown-menu slot="menu" width="200" direction="rtl">
      <ul class="list-reset">
        <li v-for="(info, market) in localesWithoutCurrent" :key="market" :data="market">
          <a href="#" @click.prevent="switchMarket(info.value)" class="block no-underline text-90 hover:bg-30 p-3">
            {{ info.description }}
          </a>
        </li>
      </ul>
    </dropdown-menu>
  </dropdown>
</template>

<script>
import _ from 'lodash'
export default {
  data: () => ({
    markets: [],
    currentMarket: ''}),
  mounted() {
    Nova.request()
        .get('/nova-vendor/market-selector/markets')
        .then(response => {
          this.markets = response.data.markets
          this.currentMarket = response.data.current
        });

  },
  computed: {
    localesWithoutCurrent() {
      return _.omit(this.markets, this.currentMarket)
    },
    nameToShow()
    {
        if (this.currentMarket !== '') {
         return  this.markets[this.currentMarket].description
        }
        return '';
    }
  },
  methods: {
    switchMarket(value) {
      Nova.request()
          .post('/nova-vendor/market-selector/switch', {
            value:value,
          })
          .then(
              response => {
                window.location.href='/cms'
              })
    }
  }
}
</script>
