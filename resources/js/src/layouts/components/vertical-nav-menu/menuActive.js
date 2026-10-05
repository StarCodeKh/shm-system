/*=========================================================================================
  File Name: menuActive.js
  Description: Works out which sidebar entry belongs to the current route.

  Child pages are matched to their menu entry by URL prefix, using the longest match:
    /officer/edit/5      -> /officer
    /officer/form        -> /officer/form   (not /officer)
    /account/income/form -> /account/income
==========================================================================================*/

import navMenuItems from './navMenuItems.js'

// Routes that render a menu entry under a different path
const ALIASES = { '/': '/dashboard' }

const children = item => [...(item.items || []), ...(item.submenu || [])]

const collectUrls = items => items.reduce((urls, item) => {
  if (item.url && item.slug !== 'external') urls.push(item.url)
  return urls.concat(collectUrls(children(item)))
}, [])

const MENU_URLS = collectUrls(navMenuItems)

export function activeMenuUrl(path) {
  const normalized = (path || '/').replace(/\/+$/, '') || '/'
  const target = ALIASES[normalized] || normalized
  let best = null
  MENU_URLS.forEach(url => {
    const matches = target === url || target.startsWith(url + '/')
    if (matches && (!best || url.length > best.length)) best = url
  })
  return best
}

// True when `item` or one of its descendants is the active entry
export function containsActive(item, path) {
  const active = activeMenuUrl(path)
  if (!active) return false
  const search = node => node.url === active || children(node).some(search)
  return children(item).some(search)
}
