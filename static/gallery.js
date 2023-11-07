let controllers = []

function main() {
	controllers.forEach(c => c.abort())
	controllers = []

	document.querySelectorAll('.gallery input[type=checkbox]').forEach((e, i) =>
		e.addEventListener('input', () => {
			if (controllers[i]) controllers[i].abort()
			controllers[i] = new AbortController()

			const data = new FormData()
			data.set('id', e.parentElement.href.split('/').pop().split('.')[0])
			if (e.checked) data.set('checked', '1')

			fetch(e.parentElement.href.split('images/')[0], {
				method: 'POST',
				body: data,
				signal: controllers[i].signal,
			})
		})
	)
}

new MutationObserver(main).observe(document.querySelector('main'), {
	childList: true,
	subtree: true,
})

main()
