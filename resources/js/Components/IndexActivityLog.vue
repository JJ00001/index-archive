<script setup>
import { Badge } from '@/Components/ui/badge'
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

const getActivityBadgeVariant = (description) => {
  return description === 'company_added_to_index' ? 'default' : 'destructive'
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
        >
          <TableCell>
            <Badge :variant="getActivityBadgeVariant(activity.description)">
              {{ getActivityLabel(activity.description) }}
            </Badge>
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