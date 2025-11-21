<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { FlexRender, getCoreRowModel, getFilteredRowModel, getSortedRowModel, useVueTable } from '@tanstack/vue-table'
import { InfiniteScroll } from '@inertiajs/vue3'
import DataTableRowSkeleton from '@/Components/ui/data-table/DataTableRowSkeleton.vue'
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import useRememberedScroll from '@/composables/useRememberedScroll'

const props = defineProps({
    // Data
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
    // Behavior
    onRowClick: {
        type: Function,
        required: true,
    },
    // Search
    enableSearch: {
        type: Boolean,
        default: false,
    },
    searchColumnKey: {
        type: String,
        default: null,
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...',
    },
    // State
    emptyStateMessage: {
        type: String,
        default: 'No results found.',
    },
    maxHeightClass: {
        type: String,
        default: 'max-h-[50vh]',
    },
    // Infinite Scroll
    infiniteScrollKey: {
        type: String,
        default: null,
    },
    infiniteScrollBuffer: {
        type: Number,
        default: 500,
    },
    rememberScrollKey: {
        type: String,
        required: true,
    },
    bodyId: {
        type: String,
        required: true,
    },
    // Loading/Skeleton
    skeletonRowCount: {
        type: Number,
        default: 5,
    },
    // Sorting
    sorting: {
        type: Array,
        default: () => [],
    },
    onSortingChange: {
        type: Function,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
})

const searchTerm = ref('')

const columns = computed(() => props.columns ?? [])
const rows = computed(() => props.data?.data ?? [])

const { scrollContainer, handleScroll, resetScrollPosition } = useRememberedScroll(
    props.rememberScrollKey,
)

const handleSortingChange = (updater) => {
    resetScrollPosition()

    props.onSortingChange?.(updater)
}

const table = useVueTable({
    get data () {
        return rows.value
    },
    get columns () {
        return columns.value
    },
    state: {
        get sorting () {
            return props.sorting
        },
    },
    manualSorting: true,
    onSortingChange: handleSortingChange,
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
})

const applySearchFilter = (value) => {
    if (!props.enableSearch || !props.searchColumnKey) {
        return
    }

    const column = table.getColumn(props.searchColumnKey)
    const filterValue = typeof value === 'string' ? value.trim() : ''
    column?.setFilterValue(filterValue)
}

watch(
    searchTerm,
    applySearchFilter,
    { immediate: true },
)

const rowClasses = computed(() =>
    props.onRowClick ? 'cursor-pointer hover:bg-muted/50' : '',
)

const handleRowClick = (row) => {
    const entity = row.original

    props.onRowClick(entity)
}

onMounted(() => {
    resetScrollPosition()
})
</script>

<template>
    <div class="space-y-3">
        <div
            v-if="enableSearch && searchColumnKey"
            class="flex items-center justify-between gap-3"
        >
            <input
                v-model="searchTerm"
                :placeholder="searchPlaceholder"
                autocapitalize="none"
                autocomplete="off"
                class="bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-ring/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 block w-full max-w-sm rounded-md border border-input px-3 py-2 text-sm shadow-xs ring-offset-background transition"
                data-1p-ignore="true"
                data-lpignore="true"
                spellcheck="false"
                type="search"
            />
        </div>

        <div
            ref="scrollContainer"
            :class="['overflow-y-auto rounded-md border border-border', maxHeightClass]"
            @scroll="handleScroll"
        >
            <InfiniteScroll
                :buffer="infiniteScrollBuffer"
                :data="infiniteScrollKey"
                :items-element="'#' + bodyId"
                preserve-url
                v-slot="{ loading: loadingInfiniteScroll }"
            >
                <Table>
                    <TableHeader>
                        <TableRow
                            v-for="headerGroup in table.getHeaderGroups()"
                            :key="headerGroup.id"
                        >
                            <TableHead
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                :class="header.column.columnDef.meta?.headerClass"
                            >
                                <template v-if="!header.isPlaceholder">
                                    <FlexRender
                                        :props="header.getContext()"
                                        :render="header.column.columnDef.header"
                                    />
                                </template>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody :id="bodyId">
                        <template v-if="table.getRowModel().rows.length && !loading">
                            <TableRow
                                v-for="row in table.getRowModel().rows"
                                :key="row.id"
                                :class="rowClasses"
                                @click="handleRowClick(row)"
                            >
                                <TableCell
                                    v-for="cell in row.getVisibleCells()"
                                    :key="cell.id"
                                    :class="cell.column.columnDef.meta?.cellClass"
                                >
                                    <FlexRender
                                        :props="cell.getContext()"
                                        :render="cell.column.columnDef.cell"
                                    />
                                </TableCell>
                            </TableRow>
                        </template>
                        <TableEmpty v-else-if="!(loading || loadingInfiniteScroll)"
                                    :colspan="table.getAllColumns().length">
                            {{ emptyStateMessage }}
                        </TableEmpty>
                        <DataTableRowSkeleton
                            v-if="loading || loadingInfiniteScroll"
                            :columns="table.getAllLeafColumns()"
                            :row-count="skeletonRowCount"
                        />
                    </TableBody>
                </Table>
            </InfiniteScroll>
        </div>
    </div>
</template>
