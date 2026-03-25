import type {ColumnSort, SortingState, Updater} from '@tanstack/vue-table'
import {ref} from 'vue'

export default function useDataTableSorting(
    sort: unknown,
    visitRoute: (params?: object, options?: { onFinish?: () => void }) => void,
) {
    const sorting = ref<SortingState>([])
    const isSorting = ref(false)

    const resolveNextSorting = (updater: Updater<SortingState>): SortingState => {
        return typeof updater === 'function' ? updater(sorting.value) : updater
    }

    const requestSorting = (columnSort: ColumnSort): void => {
        const sortParam = columnSort.desc ? `-${columnSort.id}` : columnSort.id

        isSorting.value = true

        visitRoute(
            { sort: sortParam },
            {
                onFinish: () => {
                    isSorting.value = false
                },
            },
        )
    }

    const requestDefaultSorting = (): void => {
        isSorting.value = true

        visitRoute(
            {},
            {
                onFinish: () => {
                    isSorting.value = false
                },
            },
        )
    }

    const handleSortingChange = (updater: Updater<SortingState>): void => {
        sorting.value = resolveNextSorting(updater)

        const columnSort = sorting.value[0]

        if (!columnSort?.id) {
            requestDefaultSorting()
            return
        }

        requestSorting(columnSort)
    }

    return {
        sorting,
        isSorting,
        handleSortingChange,
    }
}
