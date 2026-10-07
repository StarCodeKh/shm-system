// Column classes for dynamic form fields, shared by every form.
// Standard layout: 3 columns on desktop, 2 on tablets, 1 on phones.
// "1grid" / "full-grid" keep a single column for forms that ask for it.
// "2col" is two columns (popups pick it for medium-length forms, see DModalForm).
export const STANDARD_GRID = 'vx-col w-full md:w-1/2 lg:w-1/3 mt-4'

export function gridClass(rowDisplay) {
  if (rowDisplay === '1grid') return 'vx-col w-full mt-4'
  if (rowDisplay === '2col') return 'vx-col w-full md:w-1/2 mt-4'
  if (rowDisplay === 'full-grid') return 'vx-col w-full'
  return STANDARD_GRID
}

// Number of columns a form actually uses (popup sizing)
export function gridColumns(rowDisplay) {
  if (rowDisplay === '2col') return 2
  return rowDisplay === '1grid' || rowDisplay === 'full-grid' ? 1 : 3
}
