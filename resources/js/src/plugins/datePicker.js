// App-wide defaults for every flatpickr / <flat-pickr> instance:
// Khmer or English labels (following the current i18n locale), dd/mm/yyyy display
// with a Y-m-d model value, and a "Today / Clear" footer.
import flatpickr from 'flatpickr'
import { english } from 'flatpickr/dist/l10n/default'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import i18n from '@/i18n/i18n'

const khmer = {
    weekdays: {
        shorthand: ['អា', 'ច', 'អ', 'ព', 'ព្រ', 'សុ', 'ស'],
        longhand: ['អាទិត្យ', 'ចន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'],
    },
    months: {
        shorthand: ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
        longhand: ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'],
    },
    ordinal: () => '',
    firstDayOfWeek: 0,
    rangeSeparator: ' ដល់ ',
    weekAbbreviation: 'សប្តាហ៍',
    scrollTitle: 'រំកិលដើម្បីបង្កើន',
    toggleTitle: 'ចុចដើម្បីផ្លាស់ប្ដូរ',
    yearAriaLabel: 'ឆ្នាំ',
    time_24hr: true,
}

const LABELS = {
    kh: { today: 'ថ្ងៃនេះ', clear: 'សម្អាត' },
    en: { today: 'Today', clear: 'Clear' },
}

const isKhmer = () => i18n.global.locale !== 'en'

// Laravel serializes date casts as UTC ISO strings ("2026-08-17T17:00:00.000000Z");
// read those in local time so the picker shows the stored calendar day.
const ISO_WITH_ZONE = /T\d{2}:\d{2}.*(Z|[+-]\d{2}:?\d{2})$/

function parseDate(value, format) {
    if (typeof value === 'string' && ISO_WITH_ZONE.test(value)) {
        const date = new Date(value)
        if (!isNaN(date)) return date
    }
    return flatpickr.parseDate(value, format)
}

function footerButton(className, text, onClick) {
    const button = document.createElement('button')
    button.type = 'button'
    button.className = className
    button.textContent = text
    // Keep focus in the input so flatpickr doesn't treat the click as "outside"
    button.addEventListener('mousedown', e => e.preventDefault())
    button.addEventListener('click', onClick)
    return button
}

function shmDatePicker() {
    return fp => ({
        locale: isKhmer() ? khmer : english,
        onReady() {
            const calendar = fp.calendarContainer
            if (!calendar || calendar.querySelector('.shm-datepicker-footer')) return
            calendar.classList.add('shm-datepicker')
            // The visible alt input only copies the user's classes; tag it so the shared input styles apply
            if (fp.altInput) fp.altInput.classList.add('flatpickr-input')
            if (fp.config.noCalendar || fp.config.inline) return

            const labels = LABELS[isKhmer() ? 'kh' : 'en']
            const footer = document.createElement('div')
            footer.className = 'shm-datepicker-footer'
            footer.appendChild(footerButton('shm-datepicker-today', labels.today, () => {
                const today = new Date()
                fp.setDate(fp.config.mode === 'range' ? [today, today] : today, true)
                fp.close()
            }))
            footer.appendChild(footerButton('shm-datepicker-clear', labels.clear, () => {
                fp.clear()
                fp.close()
            }))
            calendar.appendChild(footer)
        },
    })
}

flatpickr.setDefaults({
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'd/m/Y',
    allowInput: false,
    disableMobile: true,
    monthSelectorType: 'static',
    parseDate,
    plugins: [shmDatePicker()],
})

export function installDatePicker(app) {
    app.component('flat-pickr', flatPickr)
}

export default flatpickr
