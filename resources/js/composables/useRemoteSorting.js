import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

export default function useRemoteSorting (options) {
  const {
    initialSort = () => [],
    routeName,
    data = () => ({}),
    only = [],
    reset = [],
  } = options

  const resolveInitial = () => initialSort() ?? []

  const sorting = ref(resolveInitial())

  watch(
    () => initialSort(),
    (value) => {
      sorting.value = value ?? []
    },
    { deep: true },
  )

  const applySort = (payload) => {
    sorting.value = payload ? [payload] : []

    const visitData = {
      ...data(),
    }

    if (payload) {
      visitData.sort = payload
    } else {
      delete visitData.sort
    }

    router.visit(route(routeName), {
      data: visitData,
      replace: true,
      preserveState: true,
      only,
      reset,
    })
  }

  return {
    sorting,
    applySort,
  }
}
