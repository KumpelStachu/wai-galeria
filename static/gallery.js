let controllers = []

function main() {
	controllers.forEach(c => c.abort())
	controllers = []

	document.querySelectorAll('.gallery input[type=checkbox]').forEach((e, i) =>
		e.addEventListener('input', async () => {
			if (controllers[i]) controllers[i].abort()
			controllers[i] = new AbortController()

			const data = new FormData()
			data.set('id', e.parentElement.href.split('/').pop().split('.')[0])
			if (e.checked) data.set('checked', '1')

			if (!e.checked && location.pathname.endsWith('/cart')) {
				document.body.classList.add('no')
				e.parentElement.style.display = 'none'
			}

			e.disabled = true
			e.style.cursor = 'wait'

			const res = await fetch(e.parentElement.href.split('images/')[0] + 'cart', {
				method: 'POST',
				body: data,
				signal: controllers[i].signal,
			}).catch(() => null)

			const text = await res.text()
			if (!isNaN(+text)) document.querySelector('#cart').style.display = +text ? '' : 'none'

			e.disabled = false
			e.style.cursor = ''

			if (res !== null && !e.checked && location.pathname.endsWith('/cart')) {
				if (e.parentElement.parentElement.childElementCount > 1) location.reload()
				else location = e.parentElement.href.split('images/')[0]
			}
		})
	)
}

new MutationObserver(main).observe(document.querySelector('main'), {
	childList: true,
	subtree: true,
})

main()
