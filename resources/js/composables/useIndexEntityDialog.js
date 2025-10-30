import { ref } from 'vue'

export default function useIndexEntityDialog () {
  const isOpen = ref(false)
  const isLoading = ref(false)
  const error = ref(null)

  const open = () => {
    isOpen.value = true
    isLoading.value = true
    error.value = null
  }

  const close = () => {
    isOpen.value = false
    isLoading.value = false
    error.value = null
  }

  return {
    isOpen,
    isLoading,
    error,
    open,
    close,
  }
}
