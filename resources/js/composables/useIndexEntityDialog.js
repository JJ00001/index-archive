import { ref } from 'vue'

export default function useIndexEntityDialog (fetchEntity) {
  const isOpen = ref(false)
  const isLoading = ref(false)
  const entity = ref(null)
  const error = ref(null)

  const reset = () => {
    entity.value = null
    error.value = null
  }

  const open = async (context) => {
    isOpen.value = true
    isLoading.value = true
    reset()

    try {
      entity.value = await fetchEntity(context)
    } catch (err) {
      error.value = err
    } finally {
      isLoading.value = false
    }
  }

  const close = () => {
    isOpen.value = false
    reset()
  }

  return {
    isOpen,
    isLoading,
    entity,
    error,
    open,
    close,
  }
}
