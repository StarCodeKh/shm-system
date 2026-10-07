// v-hammer:swipe.left="handler" — touch gestures (HammerJS), used to swipe the side menu open/closed
import Hammer from 'hammerjs'

const eventName = binding => binding.arg + Object.keys(binding.modifiers || {}).join('')

export default {
  install(app) {
    app.directive('hammer', {
      mounted(el, binding) {
        const manager = new Hammer.Manager(el)
        manager.add(new Hammer.Swipe({ direction: Hammer.DIRECTION_ALL }))
        el.__hammer = { manager, binding }
        // Always call the current handler (the binding is refreshed on update)
        manager.on(eventName(binding), event => {
          const current = el.__hammer && el.__hammer.binding
          if (current && current.value) current.value(event)
        })
      },
      updated(el, binding) {
        if (el.__hammer) el.__hammer.binding = binding
      },
      beforeUnmount(el) {
        if (el.__hammer) el.__hammer.manager.destroy()
        delete el.__hammer
      },
    })
  },
}
