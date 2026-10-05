// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq.js,v 1.3 2023/08/18 12:19:36 qvarin Exp $

function faq_expand_collaspe(id) {
    if (id) {
        let parent = document.getElementById("parent_question_" + id);
        let answer = document.getElementById("child_question_" + id);
        if (answer) {
            switch (answer.style.display) {
                case "block":
                    answer.style.display = "none";
                    parent.setAttribute('class', 'bg-grey');

                    if (parent.parentNode.tagName == "BUTTON") {
                        parent.parentNode.setAttribute('aria-expanded', 'false');
                    }
                    break;
                case "none":
                default:
                    answer.style.display = "block";
                    parent.setAttribute('class', 'bg-grey question_expanded');
                    if (parent.parentNode.tagName == "BUTTON") {
                        parent.parentNode.setAttribute('aria-expanded', 'true');
                    }
                    break;
            }

        }
    }
}

function faq_collapse_all_questions() {
    let childs = document.getElementsByClassName("faq_child");
    for (let i = 0; i < childs.length; i++) {
        childs[i].style.display = "none";
    }

    let parents = document.getElementsByClassName("bg-grey question_expanded");
    for (let i = 0; i < parents.length; i++) {
        parents[i].setAttribute('class', 'bg-grey');
        if (parents[i]?.parentNode?.tagName == "BUTTON") {
            parents[i].parentNode.setAttribute('aria-expanded', 'false');
        }
    }
}

function faq_expand_all_questions() {
    let childs = document.getElementsByClassName("faq_child");
    for (let i = 0; i < childs.length; i++) {
        childs[i].style.display = "block";
    }

    let parents = document.getElementsByClassName("bg-grey");
    for (let i = 0; i < parents.length; i++) {
        parents[i].setAttribute('class', 'bg-grey question_expanded');
        if (parents[i]?.parentNode?.tagName == "BUTTON") {
            parents[i].parentNode.setAttribute('aria-expanded', 'true');
        }
    }
}