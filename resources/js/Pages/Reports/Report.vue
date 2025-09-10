<script setup>

import FromGroup from "@/components/FromGroup/index.vue";
import Card from "@/components/Card/index.vue";
import VueFlatPickr from "vue-flatpickr-component";
import Icon from "@/components/Icon/index.vue";
import {startCase} from "lodash-es";
import {ref} from "vue";
import Select from "@/components/Select/index.vue";
import CustomVueSelect from "@/Pages/Components/CustomVueSelect.vue";

const props = defineProps({
  name: String,
  selects: Object,
  dateRange: {
    type: Boolean,
    default: true
  },
  singleDate: {
    type: Boolean,
    default: false
  },
  dateRequired: {
    type: Boolean,
    default: true
  },
  hidden: {
    type: Array,
    default: () => []
  }
})

const selected = ref([]);
const date = ref('');

</script>

<template>
  <Card>
    <div class="space-y-6">
      <div class="flex space-x-3 items-center rtl:space-x-reverse">
        <div
          class="flex-none h-8 w-8 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-300 flex flex-col items-center justify-center text-lg">
          <Icon icon="heroicons:building-office-2"/>
        </div>
        <div class="flex-1 text-base text-slate-900 dark:text-white font-medium capitalize">
          {{ startCase(name) }} Reports
        </div>
      </div>
      <!--          content starts here-->
      <form :action="route('reports.show', name)" class="gap-4 flex items-end justify-end" target="_blank">

        <template v-for="item in hidden">
          <input :name="item.name" :value="item.value" type="hidden">
        </template>

        <template v-for="select in selects">
          <Select v-if="select && select.options != null" v-model="selected[select.model]" :exec="!selected[select.model]?selected[select.model] = select.default:''"
                  :label="select.label"
                  :placeholder="select.placeholder" :required="select.required">
            <option v-for="value in select.options" :value="value.id">
              {{ value.value }}
            </option>
          </Select>
          <CustomVueSelect v-else-if="select" v-model="selected[select.model]" :label="select.label"
                           :multiple="select.multiple" :placeholder="select.placeholder"
                           :required="select.required" :route-name="select.remote"/>
          <input v-if="select" :name="select.model" :value="selected[select.model]?.id" type="hidden">
        </template>
        <FromGroup v-if="dateRange" :required="dateRequired" class="w-full" label="From Date">
          <VueFlatPickr
            :config="{
              mode: 'single',
              allowInput: true
              }" :required="dateRequired" autocomplete="off"
            placeholder="Select one date twice get single day report"
            class="input-control input-control w-full block focus:outline-none"
            name="from-date"
          />
        </FromGroup>
        <FromGroup v-if="dateRange" :required="dateRequired" class="w-full" label="To Date">
          <VueFlatPickr
            :config="{
              mode: 'single',
              allowInput: true,
              defaultDate: 'today',
              }" :required="dateRequired" autocomplete="off"
            class="input-control input-control w-full block focus:outline-none"
            name="to-date"
            placeholder="Select one date twice get single day report"
          />
        </FromGroup>
        <FromGroup v-if="singleDate" :required="dateRequired" class="w-full" label="Date">
          <VueFlatPickr
            v-model="date"
            :config="{
              mode: 'single',
              allowInput: true
              }" :required="dateRequired" autocomplete="off"
            class="input-control input-control w-full block focus:outline-none"
            name="single-date"
            placeholder="Select one date"
          />
        </FromGroup>
        <div class="space-x-4">
          <button class="btn btn-dark px-3 py-2">View</button>
          <!--          <button class="btn btn-dark px-3 py-2" name="action" value="download">{{ trans('Download') }}</button>-->
        </div>
      </form>
      <!--          content end here-->
    </div>
  </Card>
</template>

<style scoped>

</style>
