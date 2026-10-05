function findIconElements(node) {
    if(
        node.nodeType === Node.ELEMENT_NODE &&
        node.tagName.toLowerCase() === 'i' &&
        node.className.includes('fa fa-') && 
        !node.getAttribute('aria-hidden')
    ) {
        node.setAttribute('aria-hidden', 'true');
    }

    node.childNodes.forEach(function(childNode) {
        findIconElements(childNode);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    let targetNode = document.body;
    let config = {
        attributes: false,
        characterData: false,
        childList: true,
        subtree: true
    };
    
    var observer = new MutationObserver((mutationsList, observer) => {
        for(let mutation of mutationsList) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach((node) => {
                    findIconElements(node);
                });
            }
        }
    });

    observer.observe(targetNode, config);
});