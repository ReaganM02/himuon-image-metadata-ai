(() => {

    const renderMessage = (data) => {
        const message = document.querySelector('.himuon-image-metadata--message')
        if (!message || !(message instanceof HTMLElement)) return

        const defaultClass = 'himuon-image-metadata--message'
        message.classList.forEach((cl) => {
            if (cl !== defaultClass) {
                message.classList.remove(cl)
            }
        })

        message.style.display = 'block'
        message.classList.add(data.class)
        message.textContent = data.message
    }
    const form = document.querySelector('.himuon-image-metadata--form')
    if (!form || !(form instanceof HTMLFormElement)) return

    form.addEventListener('submit', async (e) => {
        e.preventDefault()

        /** @type {HTMLButtonElement} */
        const button = document.querySelector('.button-primary')

        const { label, labelLoading } = button.dataset

        const fd = new FormData(e.target)
        const { url } = HimuonImageMetadata
        try {
            button.disabled = true
            button.textContent = labelLoading
            const request = await fetch(url, {
                method: 'post',
                body: fd
            })
            const response = await request.json()
            renderMessage(response.data)
        } catch (error) {

        } finally {
            button.disabled = false
            button.textContent = label
        }
    })

})()
