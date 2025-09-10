<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {Head} from "@inertiajs/vue3"
import Report from "@/Pages/Reports/Report.vue";
import {startCase} from "lodash-es";

const props = defineProps({
  report: {
    type: Object,
    required: true
  }
})

const breadcrumbItems = [
  {
    title: 'Reports',
    route: {name: 'reports.index'},
  }
];

</script>

<template>
  <Head :title="startCase(report.name)+' '+ 'Reports'"/>
  <AdminLayout :breadcrumb-items="breadcrumbItems" :name="startCase(report.name)+' '+ 'Reports'">
    <Report v-if="report.include" v-bind="report"/>
  </AdminLayout>
</template>
