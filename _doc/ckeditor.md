# CKEditor

If you use CKEditor for the WYSIWYG fields of your blocks, you will need to implement a decidated Stimulus controller to
manage CKEditor instances as you add or re-render blocks by dragging them.

Here is an example of how you might do that:

```js
import { Controller } from '@hotwired/stimulus';
import { useIntersection } from 'stimulus-use';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    formJs: String,
    ckeditorConfig: String,
  };

  isDragging = false;
  isDraggable = false;
  ckConf;

  connect() {
    useIntersection(this);

    this._loadConf();
  }

  appear() {
    this._instanciate();
  }

  destroy() {
    this.isDraggable = true;
    this.isDragging = true;

    if ('undefined' !== typeof CKEDITOR) {
      if (CKEDITOR.instances[this.element.id]) {
        CKEDITOR.instances[this.element.id].destroy();
      }
    }
  }

  restore() {
    this.isDragging = false;

    if ('undefined' !== typeof CKEDITOR && this.ckeditorConfigValue) {
      this._replaceEditor();
    }
  }

  _instanciate() {
    // Si CkEditor chargé
    if (window.CKEDITOR) {
      this._initCK();
    } else {
      // sinon on charge tous les scripts
      this._initScript();
    }
  }

  _initScript() {
    const script = document.createElement('script');
    script.src = this.formJsValue;
    document.head.appendChild(script);

    this._whenDefined(window, 'CKEDITOR', () => {
      this._initCK(window.CKEDITOR);
    });
  }

  _whenDefined(context, variableName, ck) {
    // eslint-disable-next-line prefer-rest-params
    const interval = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 150;
    const checkVariable = () => {
      if (context[variableName]) {
        ck();
      } else {
        setTimeout(checkVariable, interval);
      }
    };

    setTimeout(checkVariable, 0);
  }

  _initCK() {
    if ((this.isDraggable && !this.isDragging && !(CKEDITOR.instances[this.element.id]))
      || (!this.isDraggable)) {
      this._replaceEditor();
    }
  }

  _loadConf() {
    try {
      this.ckConf = JSON.parse(this.ckeditorConfigValue);

      if (!this.ckConf?.on?.change) {
        this.ckConf = {
          ...this.ckConf,
          on: {
            // eslint-disable-next-line object-shorthand
            change: function () {
              this.updateElement();
            },
          },
        };
      }
    } catch (err) {
      console.error(`CKEditor configuration could not be parsed: ${err}`);
    }
  }

  _replaceEditor() {
    CKEDITOR.replace(this.element.id, this.ckConf);
  }
}
```

You also need to bind this controller to the CKEditor Twig widget.

```twig
{% block ckeditor_widget %}
  <textarea {{ block('widget_attributes') }} {{ stimulus_controller('ckeditor', {
    formJs: ckeditor_js_path(js_path),
    ckeditorConfig: '',
  }) }} data-action="start@window->ckeditor#destroy end@window->ckeditor#restore"
  >{{ value }}</textarea>
{% endblock %}
```

To load your configuration, you can use the `CKEditorConfiguration->getConfig()` method and `json_encode` the result.
