"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _stimulus = require("@hotwired/stimulus");

function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var _default = /*#__PURE__*/function (_Controller) {
  _inherits(_default, _Controller);

  var _super = _createSuper(_default);

  function _default() {
    _classCallCheck(this, _default);

    return _super.apply(this, arguments);
  }

  _createClass(_default, [{
    key: "connect",
    value: function connect() {
      this.indexValue = this.positionTargets.length;
      this.computeOrderOnSubmit = this.computeOrderOnSubmit.bind(this);
      this.selectTarget.form.addEventListener('submit', this.computeOrderOnSubmit);
    }
  }, {
    key: "disconnect",
    value: function disconnect() {
      this.selectTarget.form.removeEventListener('submit', this.computeOrderOnSubmit);
    }
  }, {
    key: "add",
    value: function add(e) {
      e.preventDefault(); // Get selected option value

      var selectedblockType = e.currentTarget.value;
      var selectedblockName = e.currentTarget.selectedOptions[0].dataset.name; // Get the prototype data

      var proto = this.getPrototype(selectedblockType);

      if (null === proto) {
        return;
      }

      var blockItemProto = this.panelTarget.dataset.blockItemPrototype.replace(/__type__/g, selectedblockType).replace(/__name__/g, selectedblockName).replace(/__state_class__/g, 'yellow').replace(/__position__/g, this.indexValue).replace(/__header__/g, this.newBlockValue + ' ' + selectedblockName).replace(/__body_attr__/g, '').replace(/__body__/g, proto.dataset.blockPrototype.replace(/__umanit_block__/g, this.indexValue)); // Increment the index by one for the next item

      ++this.indexValue; // Display html content (with <script> execution, necessary for WYSIWYG
      // fields managed by CKEditor, for example)

      var fragment = document.createRange().createContextualFragment(blockItemProto);
      this.panelTarget.appendChild(fragment);
      var insertedItem = this.panelTarget.lastElementChild; // Add custom javascript event on the new panel

      this.dispatch('after-add', {
        detail: {
          panel: this.panelTarget,
          item: insertedItem
        }
      }); ///////////////////////////////////////////////////////
      // COMPATIBILITY WITH EASYADMIN ///////////////////////
      ///////////////////////////////////////////////////////
      // Allows EasyAdmin JS to be bound to new blocks, without
      // the controller prefix (allows for EA types like
      // FileUploadType to be used within blocks)

      this.dispatch('ea.collection.item-added', {
        prefix: null
      }); // Scroll to newly created block

      insertedItem.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      }); // Reset select

      e.currentTarget.value = '';
    }
  }, {
    key: "computeOrderOnSubmit",
    value: function computeOrderOnSubmit() {
      var i = 0;

      var _iterator = _createForOfIteratorHelper(this.positionTargets),
          _step;

      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var position = _step.value;
          ++i;
          position.value = i;
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
    }
  }, {
    key: "getPrototype",
    value: function getPrototype(blockType) {
      var prototype = null;

      var _iterator2 = _createForOfIteratorHelper(this.prototypeTargets),
          _step2;

      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var prototypeTarget = _step2.value;

          if (blockType === prototypeTarget.dataset.blockType) {
            prototype = prototypeTarget;
            break;
          }
        }
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }

      return prototype;
    }
  }]);

  return _default;
}(_stimulus.Controller);

exports["default"] = _default;

_defineProperty(_default, "targets", ['panel', 'select', 'position', 'prototype']);

_defineProperty(_default, "values", {
  index: Number,
  newBlock: String
});