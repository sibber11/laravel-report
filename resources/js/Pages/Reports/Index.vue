<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {Head} from "@inertiajs/vue3"
import Card from "@/components/Card/index.vue";
import {startCase} from "lodash-es";
import Tooltip from "@/components/Tooltip/index.vue";
import Icon from "@/components/Icon/index.vue";

const props = defineProps({
  reports: {
    type: Object,
    required: true
  }
})

const breadcrumbItems = [];

</script>

<template>
  <Head title="Reports"/>
  <AdminLayout :breadcrumb-items="breadcrumbItems" name="Reports">
    <Card>
      <table class="vgt-table bordered">
        <thead>
        <tr>
          <th class="w-8">SL</th>
          <th>Name</th>
          <th class="w-[100px]">Action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(report, index) in reports" class="hover:bg-slate-100">
          <td class="text-center">{{ parseInt(index) + 1 }}</td>
          <td>{{ startCase(report) }} Reports</td>
          <td class="text-center">
            <Tooltip arrow placement="top" theme="dark">
              <template #button>
                <InertiaLink :href="route('reports.view', report)" class="action-btn-non-dropdown">
                  <Icon icon="heroicons:eye"/>
                </InertiaLink>
              </template>
              <span>View</span>
            </Tooltip>
          </td>
        </tr>
        </tbody>
      </table>
    </Card>
  </AdminLayout>
</template>
