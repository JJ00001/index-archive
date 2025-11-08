import { ref } from 'vue'

export default function useDataTableSorting (sort, visitRoute) {
    const sorting = ref([])

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
        handleSortingChange,
    }
}
