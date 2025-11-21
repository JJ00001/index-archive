import { ref } from 'vue'

export default function useDataTableSorting (sort, visitRoute) {
    const sorting = ref([])
    const isSorting = ref(false)

    const resolveNextSorting = (updater) => {
        const previousSorting = sorting.value
        const updatedSorting = updater(previousSorting)

        return updatedSorting ?? []
    }

    const requestSorting = (columnSort) => {
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

    const requestDefaultSorting = () => {
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
