/**
 * Class Tabs
 *
 * @see https://www.w3.org/WAI/ARIA/apg/patterns/tabs/examples/tabs-manual/
 */
class TabsManual {

    /**
     * Creates an instance of Tabs.
     *
     * @param {string} container_id
     */
    constructor(container_id) {
        this.tablist = document.querySelector('#' + container_id + ' [role=tablist]');
        this.tabs = Array.from(this.tablist.querySelectorAll('[role=tab]'));
        this.tabpanels = [];

        for (let i = 0; i < this.tabs.length; i ++) {
            const tab = this.tabs[i];
            const tabpanel = document.getElementById(tab.getAttribute('aria-controls'));

            tab.setAttribute('tabindex', '-1');
            tab.setAttribute('aria-selected', 'false');

            tab.addEventListener('keydown', this.onKeydown.bind(this));
            tab.addEventListener('click', this.onClick.bind(this));

            this.tabpanels.push(tabpanel);
        }

        this.setSelectedTab(this.firstTab);
    }

    /**
     * Returns the first tab
     *
     * @returns {HTMLElement}
     */
    get firstTab() {
        return this.tabs[0];
    }

    /**
     * Returns the last tab
     *
     * @returns {HTMLElement}
     */
    get lastTab() {
        return this.tabs[this.tabpanels.length - 1];
    }

    /**
     * Sets the selected tab
     *
     * @param {HTMLElement} currentTab
     */
    setSelectedTab(currentTab) {
        for (let i = 0; i < this.tabs.length; i ++) {
            const tab = this.tabs[i];

            if (currentTab === tab) {
                tab.setAttribute('aria-selected', 'true');
                tab.removeAttribute('tabindex');
                tab.classList.add('active');

                this.tabpanels[i].style.removeProperty('display');
            } else {
                tab.setAttribute('aria-selected', 'false');
                tab.classList.remove('active');
                tab.tabIndex = -1;

                this.tabpanels[i].style.display = 'none';
            }
        }
    }

    /**
     * Moves the focus to a tab
     *
     * @param {HTMLElement} currentTab
     */
    moveFocusToTab(currentTab) {
        currentTab.focus();
    }

    /**
     * Moves the focus to the previous tab
     *
     * @param {HTMLElement} currentTab
     */
    moveFocusToPreviousTab(currentTab) {
        if (currentTab === this.firstTab) {
            this.moveFocusToTab(this.lastTab);
        } else {
            let index = this.tabs.indexOf(currentTab);
            this.moveFocusToTab(this.tabs[index - 1]);
        }
    }

    /**
     * Moves the focus to the next tab
     *
     * @param {HTMLElement} currentTab
     */
    moveFocusToNextTab(currentTab) {
        if (currentTab === this.lastTab) {
            this.moveFocusToTab(this.firstTab);
        } else {
            let index = this.tabs.indexOf(currentTab);
            this.moveFocusToTab(this.tabs[index + 1]);
        }
    }

    /**
     * Handles the keydown event
     *
     * @param {KeyboardEvent} event
     */
    onKeydown(event) {
        const tgt = event.currentTarget;
        let flag = false;

        switch (event.key) {
            case 'ArrowLeft':
                this.moveFocusToPreviousTab(tgt);
                flag = true;
                break;

            case 'ArrowRight':
                this.moveFocusToNextTab(tgt);
                flag = true;
                break;

            case 'Home':
                this.moveFocusToTab(this.firstTab);
                flag = true;
                break;

            case 'End':
                this.moveFocusToTab(this.lastTab);
                flag = true;
                break;

            default:
                break;
        }

        if (flag) {
            event.stopPropagation();
            event.preventDefault();
        }
    }

    /**
     * Handles the click event
     *
     * @param {Event} event
     */
    onClick(event) {
        this.setSelectedTab(event.currentTarget);
    }
}