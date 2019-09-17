import {PathFinder} from "./PathFinder";

class GridStorage {


	initBehaviors() {
		// Bouton de sauvegarde
		document.getElementById('save').addEventListener('click', () => this.add());

		// Gestion de la liste.
		const list = document.querySelector('#grids');
		for (let i in this._getStorageData()) {
			const item = document.createElement("li");
			item.innerHTML = i
			item.setAttribute('data-id', i);
			item.addEventListener('click', (evt) => this.onItemClick(evt.currentTarget));
			list.appendChild(item);
		}
	}


	add() {
		const data = {
			id: '_' + (new Date()).getTime(),
			data: {
				start: PathFinder.getIds('td[data-status="3"]'),
				end: PathFinder.getIds('td[data-status="4"]'),
				obstacles: PathFinder.getIds('td[data-status="1"]')
			}
		};

		const storage = this._getStorageData();
		storage[data.id] = data;

		this._setStorageData(storage);
	}

	_getStorageData() {
		let data = {};
		try {
			data = JSON.parse(localStorage.getItem('grids'))
		} catch (e) {
		}

		return data || {};
	}

	_setStorageData(data) {
		localStorage.setItem('grids', JSON.stringify(data));
	}


	onItemClick(currentTarget) {
		const data = this._getStorageData()[currentTarget.getAttribute('data-id')];

		document.querySelectorAll('td[data-status]').forEach(item => {
			item.setAttribute('data-status', 0);
		})

		this.enable(data.data.start, 3);
		this.enable(data.data.end, 4);
		this.enable(data.data.obstacles, 1);
	}

	enable(ids, status) {
		ids.map(id=> {
			document.querySelector('[data-id="' + id + '"]').setAttribute('data-status', status);
		})

	}
}

export const storage = new GridStorage();
