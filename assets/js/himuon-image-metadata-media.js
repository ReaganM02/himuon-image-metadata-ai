
const handleGenerateMetadata = (el, imageID) => {
    const button = el.querySelector('.himuon-generate-metadata')
    console.log(button)
    if (!button || !(button instanceof HTMLButtonElement)) return

    button.addEventListener('click', async (e) => {
        e.preventDefault()
        try {
            button.disabled = true
            showScanningEffect(el)

            const { url, nonce, action } = HimuonGenerateMetaData

            const fd = new FormData()
            fd.append('action', action)
            fd.append('_wpnonce', nonce)
            fd.append('_wp_http_referer', window.location.href)
            fd.append('id', imageID)

            const request = await fetch(url, {
                method: 'post',
                body: fd
            })

            const response = await request.json()
            if (response.success) {
                updateMeta(el, response.data.alt_text, '#attachment-details-two-column-alt-text')
                updateMeta(el, response.data.title, '#attachment-details-two-column-title')
                updateMeta(el, response.data.description, '#attachment-details-two-column-caption')
                updateMeta(el, response.data.caption, '#attachment-details-two-column-description')
            }
        } catch (error) {
            console.error(error)
        } finally {
            button.disabled = false
            hideScanningEffect(el)
        }
    })
}


const updateMeta = (el, value, selector) => {
    const element = el.querySelector(selector)
    if (!element || !(element instanceof HTMLElement)) return ''
    element.value = value
}

const showScanningEffect = (el) => {
    const div = el.querySelector('.himuon-image-scan')
    if (!div || !(div instanceof HTMLDivElement)) return
    div.classList.add('himuon-scan-show')
}

const hideScanningEffect = (el) => {
    const div = el.querySelector('.himuon-image-scan')
    if (!div || !(div instanceof HTMLDivElement)) return
    div.classList.remove('himuon-scan-show')
}


const addScanningEffect = (el) => {
    const img = el.querySelector('img.details-image')
    if (!img) return

    const span = document.createElement('div')
    span.className = 'himuon-image-scan'
    img.after(span)

    const measure = () => {
        const h = img.clientHeight
        const w = img.clientWidth

        if (h === 0 || w === 0) {
            // Try again next frame
            requestAnimationFrame(measure)
            return
        }

        console.log(img)

        span.style.width = w + 'px'
        span.style.height = h + 'px'
    }

    measure()
}

const renderGenerateButton = (el) => {
    const fileURLEl = el.querySelector('.attachment-compat')
    if (fileURLEl && !el.querySelector('.himuon-image-metadata--generate-wrapper')) {
        const container = document.createElement('span')
        container.className = 'setting himuon-image-metadata--generate-wrapper'
        container.innerHTML = `
                   <label for="attachment-details-two-column-copy-link" class="name">Metadata AI:</label>
                   <span>
                    <button type="button" class="button button-large himuon-generate-metadata">Generate Metadata</button>
                   </span>
                `
        fileURLEl.before(container)
    }
}
(function ($) {
    if (!wp?.media?.view) return

    const extendView = (View) => View.extend({
        render() {
            View.prototype.render.apply(this, arguments)
            renderGenerateButton(this.el)
            handleGenerateMetadata(this.el, this.model.get('id'))

            const $img = this.$('img.details-image')

            if ($img.length && !$img.parent().hasClass('himuon-image-wrapper')) {
                $img.wrap('<div class="himuon-image-wrapper"></div>')
            }

            addScanningEffect(this.el)
            return this
        }
    })

    // Cover both layouts
    wp.media.view.Attachment.Details =
        extendView(wp.media.view.Attachment.Details)

    wp.media.view.Attachment.Details.TwoColumn =
        extendView(wp.media.view.Attachment.Details.TwoColumn)

})(jQuery)
