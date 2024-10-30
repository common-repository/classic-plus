const htmlDemoComponent = {
  template: `
    <div>
      <code>${classicPlus.code_preview}</code>
      <iframe
        ref="iframe"
        :src="url"
        :height="height"
        frameborder="0"
        scrolling="no"
        allowfullscreen="true"
        width="100%"
      ></iframe>
      <details>
        <summary>${classicPlus.view_code}</summary>
        <div v-html="innerHTML"></div>
      </details>
    </div>`,
  data() {
    return {
      url: '',
      height: '',
      innerText: '',
      innerHTML: ''
    };
  },
  watch: {
    innerText() {
      this.render()
    }
  },
  methods: {
    render() {
      const iframe = this.$refs.iframe.contentWindow.document;

      // slot to iframe
      const content = this.innerText;
      iframe.open();
      iframe.write(content);
      iframe.close();

      this.$nextTick(() => {
        // fix height
        this.height = iframe.body.scrollHeight;

        // fix height
        setTimeout(() => {
          this.height = iframe.body.scrollHeight;
        }, 300);
      });
    }
  }
}

const htmlDemoEls = document.querySelectorAll('.html-demo')
htmlDemoEls.forEach(el => {
  const innerHTML = el.innerHTML
  const innerText = el.innerText
  const vueApp = Vue.createApp(htmlDemoComponent)
  const vm = vueApp.mount(el)

  Object.assign(vm, {
    innerHTML, innerText
  })
})
