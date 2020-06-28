const delay = ms => new Promise(res => setTimeout(res, ms));
const inputsDisabled = state => document.querySelectorAll('button, input, textarea').forEach(el => {el.disabled = state;});
const reload = () => window.location.reload();
const redirect = to => window.location.assign(to);

const copy = str => {
    const el = document.createElement('textarea');

    el.value = str;
    el.setAttribute('readonly', '');
    el.style.position = 'absolute';
    el.style.left = '-9999px';
    document.body.appendChild(el);

    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
};

const api = axios.create({
    baseURL: '/',
    headers: {
        'Content-Type': 'application/json'
    },
    validateStatus: () => {
        return true;
    },
});

const formDataToJSON = data => {
    const object = {};
    [...data].map((item) => object[item[0]] = item[1]);

    return object;
}

const apiUse = (method, endpoint, data, form = null) => {
    inputsDisabled(true);

    api[method](endpoint, data).then(async response => {
        if (response.data.success) {
            try {
                M.Modal.getInstance(form).close();
            } catch (e) {/* not an modal */}
        }

        M.toast({html: response.data.message, displayLength: 8000});
        inputsDisabled(false);

        const data = response.data.data;

        if (data.prompt) {
            prompt(data.prompt.title, data.prompt.data);
        }

        if (data.redirect) {
            await delay(750);
            redirect(data.redirect);
        }

        if (data.reload) {
            await delay(750);
            reload();
        }
    });
}

const formSubmit = (form, endpoint, captchaRequired = false, method = 'post') => {
    const data = formDataToJSON(new FormData(form));

    if (captchaRequired && !data['h-captcha-response']) {
        M.toast({html: 'Please complete captcha'});
        return;
    }

    inputsDisabled(true);

    apiUse(method, endpoint, data, form);
}
