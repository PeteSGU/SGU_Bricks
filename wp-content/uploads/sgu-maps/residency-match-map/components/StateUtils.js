/**
 * StateUtils - US state code <-> full name helpers
 * Exposed as global StateUtils for use across components.
 */
(function initStateUtils() {
    const CODE_TO_NAME = {
        AL: 'Alabama',
        AK: 'Alaska',
        AZ: 'Arizona',
        AR: 'Arkansas',
        CA: 'California',
        CO: 'Colorado',
        CT: 'Connecticut',
        DE: 'Delaware',
        FL: 'Florida',
        GA: 'Georgia',
        HI: 'Hawaii',
        ID: 'Idaho',
        IL: 'Illinois',
        IN: 'Indiana',
        IA: 'Iowa',
        KS: 'Kansas',
        KY: 'Kentucky',
        LA: 'Louisiana',
        ME: 'Maine',
        MD: 'Maryland',
        MA: 'Massachusetts',
        MI: 'Michigan',
        MN: 'Minnesota',
        MS: 'Mississippi',
        MO: 'Missouri',
        MT: 'Montana',
        NE: 'Nebraska',
        NV: 'Nevada',
        NH: 'New Hampshire',
        NJ: 'New Jersey',
        NM: 'New Mexico',
        NY: 'New York',
        NC: 'North Carolina',
        ND: 'North Dakota',
        OH: 'Ohio',
        OK: 'Oklahoma',
        OR: 'Oregon',
        PA: 'Pennsylvania',
        RI: 'Rhode Island',
        SC: 'South Carolina',
        SD: 'South Dakota',
        TN: 'Tennessee',
        TX: 'Texas',
        UT: 'Utah',
        VT: 'Vermont',
        VA: 'Virginia',
        WA: 'Washington',
        WV: 'West Virginia',
        WI: 'Wisconsin',
        WY: 'Wyoming',
        DC: 'District of Columbia',

        // Canada (provinces/territories)
        AB: 'Alberta',
        BC: 'British Columbia',
        MB: 'Manitoba',
        NB: 'New Brunswick',
        NL: 'Newfoundland and Labrador',
        NS: 'Nova Scotia',
        NT: 'Northwest Territories',
        NU: 'Nunavut',
        ON: 'Ontario',
        PE: 'Prince Edward Island',
        QC: 'Quebec',
        SK: 'Saskatchewan',
        YT: 'Yukon'
    };

    const CANADA_CODES = new Set([
        'AB',
        'BC',
        'MB',
        'NB',
        'NL',
        'NS',
        'NT',
        'NU',
        'ON',
        'PE',
        'QC',
        'SK',
        'YT'
    ]);

    // Legacy/alternate codes observed in source data
    const CODE_ALIASES = {
        PQ: 'QC'
    };

    const NAME_TO_CODE = Object.fromEntries(
        Object.entries(CODE_TO_NAME).map(([code, name]) => [name.toLowerCase(), code])
    );

    function normalizeCode(value) {
        const raw = String(value || '').trim();
        if (!raw) return '';
        if (raw.length === 2) return raw.toUpperCase();
        return '';
    }

    function toCode(value) {
        const raw = String(value || '').trim();
        if (!raw) return '';

        const maybeCode = normalizeCode(raw);
        if (maybeCode && CODE_ALIASES[maybeCode]) return CODE_ALIASES[maybeCode];
        if (maybeCode && CODE_TO_NAME[maybeCode]) return maybeCode;

        const byName = NAME_TO_CODE[raw.toLowerCase()];
        if (byName) return byName;

        // Support "PA - Pennsylvania" style strings
        const match = raw.match(/^([A-Za-z]{2})\s*[-–]\s*(.+)$/);
        if (match) {
            const code = normalizeCode(match[1]);
            if (code && CODE_TO_NAME[code]) return code;
        }

        return '';
    }

    function toFullName(value) {
        const code = toCode(value);
        if (code) return CODE_TO_NAME[code] || String(value || '').trim();
        return String(value || '').trim();
    }

    function hasCode(code) {
        return Boolean(CODE_TO_NAME[String(code || '').toUpperCase()]);
    }

    function isCanadianCode(value) {
        const code = String(value || '').trim().toUpperCase();
        return CANADA_CODES.has(code);
    }

    function getRegionQuery(value) {
        const code = toCode(value);
        const name = toFullName(value);

        if (code && isCanadianCode(code)) return `${name}, Canada`;
        if (code) return `${name}, United States`;

        return name;
    }

    window.StateUtils = {
        CODE_TO_NAME,
        toCode,
        toFullName,
        hasCode,
        isCanadianCode,
        getRegionQuery
    };
})();
