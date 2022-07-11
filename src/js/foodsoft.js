//  das  javascript der foodsoft  
// copyright Fc Schinke09 2006 



function checkAll( form_id ) {
  const o = document.forms[ 'form_'+form_id ].elements;
  if (o) {
    for (let i=0; i<o.length; i++) {
      if (o[i].type === 'checkbox')
        o[i].checked = 1;
    }
  }	
  on_change( form_id );
  // if( s = document.getElementById('checkall_'+form_id) )
  //   s.className = 'button inactive';
  // if( s = document.getElementById('uncheckall_'+form_id) )
  //   s.className = 'button';
}

function uncheckAll( form_id ) {
  const o = document.forms[ 'form_'+form_id ].elements;
  if (o){
    for (let i=0; i<o.length; i++) {
      if (o[i].type === 'checkbox')
        o[i].checked = 0;
    }
  }	
  on_change( form_id );
  // if( s = document.getElementById('uncheckall_'+form_id) )
  //   s.className = 'button inactive';
  // if( s = document.getElementById('checkall_'+form_id) )
  //   s.className = 'button';
}

// neuesfenster: neues (großes) Fenster öffnen (für wiki)
//
function neuesfenster(url,name) {
  const f = window.open(url,name,"dependent=yes,toolbar=yes,menubar=yes,location=yes,resizable=yes,scrollbars=yes");
  f.focus();
}

function drop_col(self,spalten) {
  const i = document.getElementById('select_drop_cols').selectedIndex;
  const s = document.getElementById('select_drop_cols').options[i].value;
  window.location.href = self + '&spalten=' + ( spalten - parseInt(s) );
}
function insert_col(self,spalten) {
  const i = document.getElementById('select_insert_cols').selectedIndex;
  const s = document.getElementById('select_insert_cols').options[i].value;
  window.location.href = self + '&spalten=' + ( spalten + parseInt(s) );
}

function closeCurrentWindow() {
  // this function is a workaround for the spurious " 'window.close()' is not a function" -bug
  // (occurring in some uses of onClick='window.close();'; strangely, the following works:):
  window.close();
}

function on_change( id ) {
  if( id ) {
    let s;
    if( s = document.getElementById( 'submit_button_'+id ) )
      s.className = 'button';
    if( s = document.getElementById( 'reset_button_'+id ) )
      s.className = 'button';
    if( s = document.getElementById( 'floating_submit_button_'+id ) )
      s.style.display = 'inline';
  }
}

function on_reset( id ) {
  if( id ) {
    let s;
    if( s = document.getElementById( 'submit_button_'+id ) )
      s.className = 'button inactive';
    if( s = document.getElementById( 'reset_button_'+id ) )
      s.className = 'button inactive';
    if( s = document.getElementById( 'floating_submit_button_'+id ) )
      s.style.display = 'none';
  }
}

function submit_form( form_id ) {
  const f = document.getElementById( 'form_'+form_id );
  // calling f.submit() will not trigger the onsubmit() handler implicitly, so we call it explicitly:
  if( f.onsubmit )
    f.onsubmit();
  f.submit();
}

function post_action( action, message ) {
  const f = document.forms['update_form'];
  f.action.value = action;
  f.message.value = message;
  if( f.onsubmit )
    f.onsubmit();
  f.submit();
}

function set_footbar( enabled ) {
  const footbar = document.getElementById( 'footbar' );
  footbar.style.display = enabled ? 'block' : 'none';
  updateWindowHeight();
}

function updateWindowHeight() {
  const spaceForScrollbar = 16;
  const overlap = 0.05;
  const footbar = $('footbar');
  const footbarHeight = footbar.offsetHeight;
  const windowHeight = document.viewport.getHeight();
  
  scroller.setPageHeight((1-overlap) * (windowHeight - footbarHeight - spaceForScrollbar));
}

function set_class( node, className, enabled ) {
  if (enabled) {
    node.classList.add(className);
  } else {
    node.classList.remove(className);
  }
}

function handleTextFieldKeyPress(event, onEnter) {
    if (event.key !== 'Enter') {
      return;
    }
    event.stop(); // no submit
    event.target.select();
    onEnter(true);
}

function installTextFieldChangeHandler(element, handler, captureEnter) {
  // FIXME: Why not just set 'true' as default value in the signature?
  captureEnter = typeof captureEnter === 'undefined' ? true : captureEnter;
  element.on('change', () => handler(false) );
  if (captureEnter)
    element.on(
        'keypress', 
        (event) => handleTextFieldKeyPress(event, handler)
    );
}

class Scroller {
  constructor() {
    this.mPageHeight = window.innerHeight;
    this.mKeyState = Scroller.UP;
    this.mKey = 0;
    this.mKeyPressHandler = this.handleKey.bind(this, Scroller.PRESS);
    this.mKeyDownHandler = this.handleKey.bind(this, Scroller.DOWN);
    this.mKeyUpHandler = this.handleKey.bind(this, Scroller.UP);
  }

  setPageHeight(pageHeight) {
    this.mPageHeight = pageHeight;
  }

  scrollPage(direction) {
    window.scrollBy(0, direction * this.mPageHeight);
  }

  handleKey(what, event) {
    // capture only page up / down
    if (event.key !== 'PageUp'
        && event.key !== 'PageDown'
        || event.altKey || event.ctrlKey || event.shiftKey) {
      return;
    }
    
    // check target, only want top-level scrolls
    if (this.isInNestedScrollview(event.target)) {
      return;
    }

    event.stop();
    
    if (this.mKey === event.key
        && this.mKeyState === Scroller.DOWN
        && what === Scroller.PRESS) {
      // discard first press after down:
      // firefox fires: "down, press, up" on single press, "down, press, press, ... , up" on auto-repeat
      // webkit fires: "down" on single press, "down, down, ..." on auto-repeat
      this.mKeyState = what;
      return;
    }
    
    this.mKeyState = what;
    this.mKey = event.key;
    
    if (what === Scroller.UP) {
      return;
    }

    this.scrollPage(event.key === 'PageUp' ? -1 : 1);
  }

  register(element) {
    if (element === null) {
      element = document;
    }
    Event.observe(element, 'keypress', this.mKeyPressHandler);
    Event.observe(element, 'keydown', this.mKeyDownHandler);
    Event.observe(element, 'keyup', this.mKeyUpHandler);
  }

  unregister(element) {
    if (element === null) {
      element = document;
    }
    Event.stopObserving(element, 'keypress', this.mKeyPressHandler);
    Event.stopObserving(element, 'keydown', this.mKeyDownHandler);
    Event.stopObserving(element, 'keyup', this.mKeyUpHandler);
  }

  isInNestedScrollview(node) {
    switch(node) {
      case null:
      case document.documentElement: // firefox
      case document.body: // webkit
        // top-level element: not nested
        return false;
    }
    if (node.nodeType === Node.ELEMENT_NODE) {
      if (node.nodeName === "FORM") { // have bogus sizes on IE
        return this.isInNestedScrollview(node.parentNode);
      }
      if (node.scrollHeight > node.offsetHeight) {
        return true;
      }
    }
    return this.isInNestedScrollview(node.parentNode);
  }
}

Scroller.UP = 0;
Scroller.DOWN = 1;
Scroller.PRESS = 2;

window.scroller = new Scroller();

class MagicCalculator {
  constructor(orderId, productId, distMult, endPrice)
  {
    this.mOrderId = orderId;
    this.mProductId = productId;
    this.mDistMult = distMult;
    this.mEndPrice = endPrice;
    this.mGroupFields = [];
    this.mGroupValues = [];
    this.mResultGroupValues = [];
    this.mTrashField = '';
    this.mTrashValue = 0;
    this.mBazaarField = '';
    this.mBazaarValue = 0;
    this.mBazaarTarget = 0;
    this.mTotal = 0;
    this.mUiEnabled = false;
    this.mNotInteger = false;
  }

  addGroupField(id)
  {
    this.mGroupFields.push(id);
  }

  setTrashField(id)
  {
    this.mTrashField = id;
  }

  setBazaarField(id)
  {
    this.mBazaarField = id;
  }
  parseValue(string) {
    const resultInt = parseInt(string, 10);
    const resultFloat = parseFloat(string);
    if (isNaN(resultInt) || resultInt !== resultFloat || string.indexOf('.') >= 0) {
      this.mNotInteger = true;
      return Math.round(resultFloat*1000)/1000;
    }
    return resultInt;
  }

  fetchValues()
  {
    this.mNotInteger = false;
    const deliveredQuantity = document.getElementById('liefermenge_' + this.mOrderId + '_' + this.mProductId);
    this.mTotal = this.parseValue(deliveredQuantity.value);
    this.mGroupValues.length = this.mGroupFields.length;
    for (let i = 0; i < this.mGroupFields.length; ++i)
    {
      let el = document.getElementById('menge_' + this.mGroupFields[i]);
      this.mGroupValues[i] = this.parseValue(el.value);
    }

    this.mResultGroupValues = this.mGroupValues;
    const trashQuantity = document.getElementById('menge_' + this.mTrashField).value;
    this.mTrashValue = this.parseValue(trashQuantity);
    const bazaarQuantity = document.getElementById('menge_' + this.mBazaarField).textContent;
    this.mBazaarValue = parseFloat(bazaarQuantity);
    const bazaarTarget = document.getElementById('magic_' + this.mBazaarField).value;
    this.mBazaarTarget = this.parseValue(bazaarTarget);
  }

  recalcCurrentBazaar() {
    this.mBazaarValue = this.mTotal;
    for (let i = 0; i < this.mGroupValues.length; ++i)
    {
      this.mBazaarValue -= this.mGroupValues[i];
    }
    this.mBazaarValue -= this.mTrashValue;
  }

  formatNumber(raw, precision) {
    return raw.toFixed(precision).replace(/\.?0+$/, '');
  }

  publishCurrentBazaar() {
    const bazaarQuantityEl = document.getElementById('menge_' + this.mBazaarField);
    bazaarQuantityEl.textContent = this.formatNumber(this.mBazaarValue, 3);
  }

  calculate()
  {
    if (isNaN(this.mBazaarTarget)) {
      return;
    }
    
    const fixPointFactor = (this.mNotInteger) ? 1000 : 1;
    let groupsSum = this.mGroupValues.reduce((partial, x) => partial + x, 0);
    const groupsTarget = this.mTotal - this.mBazaarTarget - this.mTrashValue;
    const ratio = groupsTarget / groupsSum;
    groupsSum = 0;
    const groupValuesExact = this.mGroupValues.map(x => x * ratio);
    this.mResultGroupValues = groupValuesExact.map(x => {
      const newX = Math.round(x * fixPointFactor) / fixPointFactor;
      groupsSum += newX;
      return newX; 
    });

    // in case of decimals, do the rounding on 1e-3, scale up, do it in integer, then scale down
    this.mBazaarValue = Math.round((this.mTotal - this.mTrashValue - groupsSum) * fixPointFactor);
    this.mBazaarTarget = Math.round(this.mBazaarTarget * fixPointFactor);
    // rounding fix-up: make array with same length initialized to zero
    let roundingDistribution = this.mGroupValues.map(() => 0);
    while (this.mBazaarValue !== this.mBazaarTarget) {
      // bazaar rest from rounding
      // direction +1: need to distribute more to groups
      const direction = (this.mBazaarValue - this.mBazaarTarget > 0) ? 1 : -1;
      let minBadness;
      let iMinBadness = 0;
      for (let i = 0; i < this.mGroupValues.length; ++i) {
        if (this.mGroupValues[i] === 0) {
          // do not involve new groups
          continue;
        }
        const badness = Math.abs(
            (this.mResultGroupValues[i] + (roundingDistribution[i] + direction)/fixPointFactor - groupValuesExact[i]) 
                / groupValuesExact[i]);
        if (i === 0) {
          minBadness = badness;
          continue;
        }
        if (badness < minBadness) {
          iMinBadness = i;
          minBadness = badness;
        }
      }
      roundingDistribution[iMinBadness] += direction;
      this.mBazaarValue -= direction;
    }
    
    for (let i = 0; i < this.mGroupValues.length; ++i) {
      this.mResultGroupValues[i] += roundingDistribution[i] / fixPointFactor;
    }
    this.mBazaarTarget /= fixPointFactor;
    this.mBazaarValue /= fixPointFactor;
  }

  setUi(enabled) {
    const magicEl = document.getElementById('magic_' + this.mOrderId + '_' + this.mProductId + '_style');
    magicEl.sheet.cssRules[0].style.display = enabled ? '' : 'none';
    this.mUiEnabled = enabled;
  }

  displayResult() {
    for (let i = 0; i < this.mGroupFields.length; ++i) {
      let groupField = document.getElementById('magic_' + this.mGroupFields[i]);
      groupField.textContent = this.formatNumber(this.mResultGroupValues[i], 3);
    }
    let magicTrash = document.getElementById('magic_' + this.mTrashField);
    magicTrash.textContent = this.formatNumber(this.mTrashValue, 3);
  }

  applyResult() {
    this.setUi(false);
    for (let i = 0; i < this.mGroupFields.length; ++i) {
      let groupField = document.getElementById('menge_' + this.mGroupFields[i]);
      groupField.value = this.formatNumber(this.mResultGroupValues[i], 3);
    }
    this.handleChangedDistribution();
  }

  initUi() {
    this.fetchValues();
    this.recalcCurrentBazaar();
    this.publishCurrentBazaar();
    this.mBazaarTarget = this.mBazaarValue;
    let magicBazaar = document.getElementById('magic_' + this.mBazaarField);
    magicBazaar.value = this.formatNumber(this.mBazaarTarget, 3);
    this.calculate();
    this.displayResult();
    this.setUi(true);
  }

  updateUi() {
    this.fetchValues();
    this.calculate();
    this.displayResult();
  }

  calcPrice(amount) {
    return this.mEndPrice * amount / this.mDistMult;
  }

  formatPrice(price) {
    return price.toFixed(2);
  }

  recalcAndShowPrices() {
    let priceTotalEl = document.getElementById('preis_' + this.mOrderId + '_' + this.mProductId);
    priceTotalEl.textContent = this.formatPrice(this.calcPrice(this.mTotal));
    for (let i = 0; i < this.mGroupFields.length; ++i) {
      let priceFieldEl = document.getElementById('preis_' + this.mGroupFields[i]);
      priceFieldEl.textContent = this.formatPrice(this.calcPrice(this.mGroupValues[i]));
    }
    let priceTrashEl = document.getElementById('preis_' + this.mTrashField);
    priceTrashEl.textContent = this.formatPrice(this.calcPrice(this.mTrashValue));
    let priceBazaarEl = document.getElementById('preis_' + this.mBazaarField);
    priceBazaarEl.textContent = this.formatPrice(this.calcPrice(this.mBazaarValue));
  }

  handleChangedDistribution() {
    this.fetchValues();
    this.recalcCurrentBazaar();
    this.publishCurrentBazaar();
    this.recalcAndShowPrices();
    if (this.mUiEnabled) {
      this.calculate();
      this.displayResult();
    }
  }
}

function bound(min, x, max) {
  x = x < min ? min : x;
  x = x > max ? max : x;
  return x;
}

class SearchableSelect {
  constructor(selectElement, searchInput) {
    const self = this;
    this.mSelectElement = selectElement;
    this.mSearchInput = searchInput;
    this.mListEntries = [];
    this.mVisibleEntries = [];
    this.mCaseSensitive = false;
    
    installTextFieldChangeHandler(
        this.mSearchInput, 
        () => self.filterList()
    );
    this.mSelectElement.on('change', () => self.emitSelection());
  }

  updateText(entry) {
    entry.data.setOption(entry.option);
    entry.option.memo = entry.data;
    if (this.mCaseSensitive)
      entry.searchText = entry.option.text;
    else
      entry.searchText = entry.option.text.toLowerCase();
  }

  setEntries(entries) {
    const self = this;
    this.mListEntries = entries.collect(function(entry) {
      return { 
        data: entry,
        option: document.createElement('option')
      };
    });
    this.mListEntries.each(function(entry) {
      self.updateText(entry);
    });
    this.mVisibleEntries = this.mListEntries.clone();
    this.updateSelectElement();
  }

  appendEntry(entry) {
    const newListEntry = {
      data: entry,
      option: document.createElement('option')
    };
    this.mListEntries.push(newListEntry);
    this.updateText(newListEntry);
    this.filterList();
  }

  remove(entry) {
    const listEntry = this.mListEntries.detect(function(it) {
      return it.data === entry;
    });
    this.mListEntries = this.mListEntries.without(listEntry);
    this.mVisibleEntries = this.mVisibleEntries.without(listEntry);
    let i = this.mSelectElement.selectedIndex;
    this.updateSelectElement();
    i = bound(0, i, this.mSelectElement.options.length - 1);
    this.selectIndex(i);
  }

  selectIndex(index) {
    if (index === this.mSelectElement.selectedIndex)
      return;
    this.mSelectElement.selectedIndex = index;
    this.emitSelection();
  }

  select(entry) {
    const self = this;
    let found = false;
    this.mVisibleEntries.each(function (listEntry, index) {
      if (listEntry.data === entry) {
        self.selectIndex(index);
        found = true;
        throw $break;
      }
    });
    if (!found) {
      // remove filter
      if (this.mSearchInput.value !== '') {
        this.mSearchInput.value = '';
        this.filterList();
        this.select(entry);
      }
      /*
      // force append to visible list
      this.mListEntries.each(function (listEntry) {
        if (listEntry.data === entry) {
          self.mVisibleEntries.push(listEntry);
          self.updateSelectElement();
          self.select(entry);
          found = true;
          throw $break;
        }
      });
      */
    }
  }

  moveSelection(delta) {
    const oldIndex = this.mSelectElement.selectedIndex;
    const newIndex = bound(
        0,
        oldIndex + delta,
        this.mSelectElement.options.length - 1);
    if (newIndex !== oldIndex) {
      this.mSelectElement.selectedIndex = newIndex;
      this.emitSelection();
      return true;
    }
    return false;
  }

  updateEntry(entry) {
    const listEntry = this.mListEntries.detect(function (x) {
      return x.data === entry;
    })
    this.updateText(listEntry);
  }

  filterList() {
    let searchText = this.mSearchInput.value;
    if (!this.mCaseSensitive)
      searchText = searchText.toLowerCase();
    this.mVisibleEntries = this.mListEntries.findAll(function(entry) {
      return ! (searchText.length && entry.searchText.indexOf(searchText) < 0);
    });
    this.updateSelectElement();
  }

  updateSelectElement() {
    var self = this;
    const currentMemo = this.currentMemo();
    this.mSelectElement.innerHTML = '';
    this.mVisibleEntries.each(function(entry) {
      self.mSelectElement.appendChild(entry.option);
      if (currentMemo === entry.option.memo) {
        self.mSelectElement.selectedIndex 
            = self.mSelectElement.options.length - 1;
        self.emitSelection();
      }
    });
  }

  currentMemo() {
    const selectedIndex = this.mSelectElement.selectedIndex;
    return selectedIndex < 0 
        ? null 
        : this.mSelectElement.options[selectedIndex].memo;
  }

  emitSelection() {
    this.mSelectElement.fire('option:selected', this.currentMemo());
  }
}

function disableAutocomplete(element) {
  element.setAttribute('autocomplete', 'off');
}
