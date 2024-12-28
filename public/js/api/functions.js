function isoToLocalDate(isoDate){
    const date = new Date(isoDate);
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return date.toLocaleDateString('uk-UA', options)
}

function isoToLocalDateTime(isoDate){
    const date = new Date(isoDate);
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return date.toLocaleDateString('uk-UA', options)
}

function formatTime(date) {
    const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return date.toLocaleTimeString('uk-UA', options);
}

function redirect(url) {
    window.location.href = url;
}

function getFunctionValues(obj) {
    const result = {};

    for (const key in obj) {
        const value = obj[key];

        if (typeof value === "function") {
            result[key] = value();
        } else if (typeof value === "object") {
            result[key] = getFunctionValues(value);
        } else {
            result[key] = value;
        }
    }

    return result;
}

function renderCategoriesSelect(categories, level = 0) {
    let html = "";
    for (const category of categories) {
        const padding = level > 0 ? "-".repeat(level) : ""; // Adjust padding as needed (10px per level)
        html += `<option value="${category.category.id}">${padding+category.category.title}</option>`;
        if (category.children.length > 0) {
            html += renderCategoriesSelect(category.children, level + 1);
        }
    }
    return html;
}

function updateCategoriesSelect(select_id = "#category-select", nullFirst = true){
    $.getJSON(
        '/api/categories/getTree',
        function (json){
            const $select = $(select_id);
            if (nullFirst){
                $select.html("<option value=\"null\" selected></option>"+renderCategoriesSelect(json.tree));
            }else{
                $select.html(renderCategoriesSelect(json.tree));
            }

        }
    )
}


export {
    isoToLocalDate,
    isoToLocalDateTime,
    formatTime,
    redirect,
    getFunctionValues,
    updateCategoriesSelect
}