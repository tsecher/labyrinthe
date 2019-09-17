export class PathFinder {

	getResult() {
		const url = '/grid/findPath';
		const data = {
			start: PathFinder.getIds('td[data-status="3"]'),
			end: PathFinder.getIds('td[data-status="4"]'),
			obstacles: PathFinder.getIds('td[data-status="1"]')
		}

		let request = new Request(url, {
			method: 'POST',
			body: data,
			headers: new Headers()
		});

		fetch(url + "?data=" + JSON.stringify(data)
		)
			.then((resp) => resp.json())
			.then((result) => this.onResult(result));
	}

	static getIds(selector) {
		const result = [];
		document.querySelectorAll(selector).forEach(item => {
			result.push(item.getAttribute('data-id'))
		});

		return result;
	}

	onResult(result) {
		// on efface :
		document.querySelectorAll('[data-status="5"]').forEach(item => {
			item.setAttribute('data-status', 0);
		});

		let index = 0;
		result.map((id) => {
			const elem = document.querySelector('[data-id="' + id + '"]');
			if (elem.getAttribute('data-status') == 0) {
				elem.setAttribute('data-status', 5)
				elem.innerHTML=(index++);
			}

		})
	}
}