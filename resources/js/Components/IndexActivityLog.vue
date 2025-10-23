<script setup>
import { Minus, Plus } from 'lucide-vue-next'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  activities: {
    type: Array,
    required: true,
  },
})

const { t } = useI18n()

const activityTypes = {
  ADDED: 'company_added_to_index',
  REMOVED: 'company_removed_from_index',
}

const activityStyles = {
  [activityTypes.ADDED]: {
    row: 'border-l-2 border-l-green-500',
    icon: 'bg-green-100 text-green-700',
    text: 'text-green-900',
    iconComponent: Plus,
  },
  [activityTypes.REMOVED]: {
    row: 'border-l-2 border-l-red-500',
    icon: 'bg-red-100 text-red-700',
    text: 'text-red-900',
    iconComponent: Minus,
  },
}

const getActivityStyle = (description) => {
  return activityStyles[description] || activityStyles[activityTypes.ADDED]
}

const getActivityLabel = (description) => {
  return t(`activity.${description}`)
}
</script>

<template>
  <div v-if="activities.length === 0"
       class="text-sm text-muted-foreground">
    {{ $t('activity.noRecentActivity') }}
  </div>
  <div v-else
       class="max-h-[550px] overflow-auto">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead class="w-2/12">{{ $t('activity.action') }}</TableHead>
          <TableHead class="w-8/12">{{ $t('company.name') }}</TableHead>
          <TableHead class="w-2/12">{{ $t('activity.dataDate') }}</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow
            v-for="activity in activities"
            :key="activity.id"
            :class="getActivityStyle(activity.description).row"
        >
          <TableCell>
            <div class="flex items-center gap-2 text-sm font-medium">
              <div
                  :class="['flex size-5 shrink-0 items-center justify-center rounded-full', getActivityStyle(activity.description).icon]"
              >
                <component :is="getActivityStyle(activity.description).iconComponent"
                           class="size-3.5" />
              </div>
              <span :class="getActivityStyle(activity.description).text">
                {{ getActivityLabel(activity.description) }}
              </span>
            </div>
          </TableCell>
          <TableCell>
            <CompanyDisplay :company="activity.company" />
          </TableCell>
          <TableCell class="text-sm text-muted-foreground">
            {{ activity.date }}
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>