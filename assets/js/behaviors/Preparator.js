import {PathFinder} from "./PathFinder";

export class Preparator {

	initButton() {
		// Initialisation des comportements des boutons
		this.buttons = document.querySelectorAll('button');
		this.buttons.forEach((button) => {
			button.addEventListener('click', (evt) => this.onButtonClick(evt.currentTarget))
		})

		// Initialisation des comportements des tds.
		this.tds = document.querySelectorAll('td');
		this.tds.forEach((td)=> this.initTd(td))

		// Initialisation du formulaire de calcule.
		document.getElementById('search').addEventListener('click', () => this.searchResult());
	}

	/**
	 *
	 */
	disableAllButtons(){
		this.buttons.forEach((button)=> {
			button.setAttribute('data-enabled', 0);
		})
	}

	/**
	 *
	 * @param target
	 */
	onButtonClick(target) {
		this.disableAllButtons();
		target.setAttribute('data-enabled', 1);

		this.currentTarget = target;
	}


	initTd(td) {
		td.addEventListener('mousedown', () =>{

			if(this.currentTarget && this.currentTarget.getAttribute('data-several') == 1){
				this.deleteMode = td.getAttribute('data-status') == this.currentTarget.getAttribute('data-several');
				if (this.deleteMode){
					td.setAttribute('data-status', 0)
				}
				else{
					td.setAttribute('data-status', this.currentTarget.getAttribute("data-status"))
				}

				this.severalEnabled = true;
			}
		});
		td.addEventListener('mouseenter', () => {
			if( this.severalEnabled ){
				if(this.currentTarget && this.currentTarget.getAttribute('data-several') == 1){
					if( this.deleteMode ){
						td.setAttribute('data-status', 0)
					}
					else{
						td.setAttribute('data-status', this.currentTarget.getAttribute("data-status"))
					}
				}
			}
		});
		td.addEventListener('mouseup', ()=> {
			this.severalEnabled = false;

			const value = this.currentTarget.getAttribute("data-status");
			if(this.currentTarget){
				if(this.deleteMode || ( value == td.getAttribute('data-status') && this.currentTarget.getAttribute('data-status') != 1 )) {
					td.setAttribute('data-status', 0);
				}
				else{

					if (this.currentTarget.getAttribute('data-several') != 1 ){
						document.querySelectorAll('td[data-status="' +value+ '"]').forEach(((item)=> { item.setAttribute('data-status', 0)}))
					}
					td.setAttribute('data-status', value)
				}
			}

		})
	}

	searchResult() {
		this.pathFinder = new PathFinder();
		this.pathFinder.getResult();
	}
}