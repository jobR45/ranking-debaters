export async function fetchAllPaginatedData(endpoint, options = {}) {
  const url = new URL(endpoint, window.location.origin)
  const items = []
  let currentPage = 1
  let lastPage = 1

  url.searchParams.set('per_page', '100')

  while (currentPage <= lastPage) {
    url.searchParams.set('page', String(currentPage))

    const response = await fetch(url, options)
    if (!response.ok) {
      throw new Error(`Failed to load paginated data: ${response.status}`)
    }

    const pagination = await response.json()
    if (!Array.isArray(pagination.data) || typeof pagination.last_page !== 'number') {
      throw new Error('The paginated API response has an invalid format.')
    }

    items.push(...pagination.data)
    lastPage = pagination.last_page
    currentPage += 1
  }

  return items
}
