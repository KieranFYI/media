import {createApp} from 'vue';
import lodash from 'lodash';
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const app = createApp({});

app.config.globalProperties.$axios = axios;
app.config.globalProperties.$lodash = lodash;

app.mixin({
    methods: {
        formatDate: function (date) {
            return new Date(date).toLocaleString('sv');
        },
        formatNumber: function (number, decimals, dec_point, thousands_sep) {
            // Strip all characters but numerical ones.
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        },
    },
});

const requireComponent = require.context(
    // The relative path of the components folder
    './components',
    // Whether or not to look in subfolders
    true,
    // The regular expression used to match base component filenames
    /[A-Z]\w+\.(vue|js)$/
);

requireComponent.keys().forEach(fileName => {
    // Get component config
    const componentConfig = requireComponent(fileName);

    // Get PascalCase name of component
    const componentName = lodash.upperFirst(
        lodash.camelCase(
            // Gets the file name regardless of folder depth
            fileName
                .split('/')
                .pop()
                .replace(/\.\w+$/, '')
        )
    );

    // Register component globally
    app.component(
        componentName,
        // Look for the component options on `.default`, which will
        // exist if the component was exported with `export default`,
        // otherwise fall back to module's root.
        componentConfig.default || componentConfig
    );
});

app.mount('#vuejs-media');
