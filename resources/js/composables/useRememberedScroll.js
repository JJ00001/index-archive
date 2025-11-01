import { useRemember } from '@inertiajs/vue3'
import { onBeforeUnmount, onMounted, ref } from 'vue'

export default function useRememberedScroll (key) {
  const rememberKey = `remembered-scroll:${key}`
  const state = useRemember({ top: 0 }, rememberKey)
  const scrollContainer = ref(null)

  const remember = () => {
    state.top = scrollContainer.value.scrollTop
  }

  const restore = async () => {
    scrollContainer.value.scrollTop = state.top
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
  }
}
