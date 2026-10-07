// added so later we can keep breakpoint in sync automatically using this config file
// import tailwindConfig from "../../tailwind.config.js"

const getters = {

	// COMPONENT
		// vx-autosuggest
	// starredPages: state => state.navbarSearchAndPinList.data.filter((page) => page.highlightAction),
  windowBreakPoint: state => {

    // This should be same as tailwind. So, it stays in sync with tailwind utility classes
    if (state.windowWidth >= 1200) return "xl"
    else if (state.windowWidth >= 992) return "lg"
    else if (state.windowWidth >= 768) return "md"
    else if (state.windowWidth >= 576) return "sm"
    else return "xs"
  },

  // Signed-in user, shown the same way as on their profile page: the person's name (Khmer, or Latin
  // when the app is in English), else the login name; photo from `avatar`; initials from the Latin name
  activeUserName: state => locale => {
    const u = state.AppActiveUser || {}
    return (locale === 'en' ? u.latin || u.fullname : u.fullname || u.latin) || u.username || ''
  },
  activeUserAvatar: state => {
    const u = state.AppActiveUser || {}
    return u.avatar || u.photo || ''
  },
  activeUserInitials: state => {
    const u = state.AppActiveUser || {}
    const name = (u.latin || u.fullname || u.username || '?').trim()
    return name.split(/\s+/).slice(0, 2).map(part => part.charAt(0)).join('').toUpperCase()
  },
}

export default getters
