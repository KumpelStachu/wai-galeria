let controller = new AbortController()

document.querySelector('input[type=search]').addEventListener('keyup', async e => {
	const { value } = e.target

	controller.abort()
	controller = new AbortController()

	const data = new FormData()
	data.set('search', value)

	const res = await fetch(location, {
		method: 'POST',
		body: data,
		signal: controller.signal,
	})
	const html = await res.text()

	document.querySelector('#results').innerHTML = html
})
