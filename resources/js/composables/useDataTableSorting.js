import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export default function useDataTableSorting (sort, visitRoute) {
    const sorting = ref([])
    const isSorting = ref(false)

    router.on('start', () => {
        isSorting.value = true
    })

    router.on('finish', () => {
        isSorting.value = false
    })

    const resolveNextSorting = (updater) => {
        const previousSorting = sorting.value
        const updatedSorting = updater(previousSorting)

        return updatedSorting ?? []
    }

    const requestSorting = (columnSort) => {
        const sortParam = columnSort.desc ? `-${columnSort.id}` : columnSort.id

        visitRoute({ sort: sortParam })
    }

    const requestDefaultSorting = () => {
        visitRoute()
    }

    const handleSortingChange = (updater) => {
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
