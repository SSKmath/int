function createElementWithClass(tag, className = '') {
    const element = document.createElement(tag);
    if (className) {
        element.className = className;
    }
    return element;
}

async function postData(data, url = '../../index.php') {
    console.log(data.type);
    const response = await fetch(url, {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        throw new Error('HTTP error! status: ${response.status}');
    }

    return response.json();
}

function scrollToBottom(element) {
    element.scrollTop = element.scrollHeight;
}