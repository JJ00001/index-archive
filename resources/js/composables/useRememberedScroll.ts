import {useRemember} from '@inertiajs/vue3'
import {onBeforeUnmount, onMounted, ref} from 'vue'

export default function useRememberedScroll(key: string) {
  const rememberKey = `remembered-scroll:${key}`
  const state = useRemember({ top: 0 }, rememberKey)
    const scrollContainer = ref<HTMLElement | null>(null)
  const storage = window.sessionStorage

  const remember = () => {
      if (!scrollContainer.value) {
          return
      }

    storage.setItem(rememberKey, String(state.top))
  }

  const restore = async () => {
      if (!scrollContainer.value) {
          return
      }

    const storedTop = storage.getItem(rememberKey)
    const parsedTop = Number(storedTop)

    scrollContainer.value.scrollTop = parsedTop
  }

  const resetScrollPosition = () => {
      if (!scrollContainer.value) {
          return
      }

      rememberedState.top = 0

    storage.setItem(rememberKey, '0')

    scrollContainer.value.scrollTop = 0
  }

  onMounted(() => {
    void restore()
  })

  onBeforeUnmount(() => {
    remember()
  })

  const handleScroll = () => {
    remember()
  }

  return {
    scrollContainer,
    handleScroll,
    resetScrollPosition,
  }
}
