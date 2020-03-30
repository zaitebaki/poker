exports.preloader = function() {
  if (document.images) {
    const img1_bubi = new Image();
    const img2_bubi = new Image();
    const img3_bubi = new Image();
    const img4_bubi = new Image();
    const img5_bubi = new Image();
    const img6_bubi = new Image();
    const img7_bubi = new Image();
    const img8_bubi = new Image();
    const img9_bubi = new Image();
    const img10_bubi = new Image();
    const img11_bubi = new Image();
    const img12_bubi = new Image();
    const img13_bubi = new Image();

    const img1_chervi = new Image();
    const img2_chervi = new Image();
    const img3_chervi = new Image();
    const img4_chervi = new Image();
    const img5_chervi = new Image();
    const img6_chervi = new Image();
    const img7_chervi = new Image();
    const img8_chervi = new Image();
    const img9_chervi = new Image();
    const img10_chervi = new Image();
    const img11_chervi = new Image();
    const img12_chervi = new Image();
    const img13_chervi = new Image();

    const img1_kresti = new Image();
    const img2_kresti = new Image();
    const img3_kresti = new Image();
    const img4_kresti = new Image();
    const img5_kresti = new Image();
    const img6_kresti = new Image();
    const img7_kresti = new Image();
    const img8_kresti = new Image();
    const img9_kresti = new Image();
    const img10_kresti = new Image();
    const img11_kresti = new Image();
    const img12_kresti = new Image();
    const img13_kresti = new Image();

    const img1_vini = new Image();
    const img2_vini = new Image();
    const img3_vini = new Image();
    const img4_vini = new Image();
    const img5_vini = new Image();
    const img6_vini = new Image();
    const img7_vini = new Image();
    const img8_vini = new Image();
    const img9_vini = new Image();
    const img10_vini = new Image();
    const img11_vini = new Image();
    const img12_vini = new Image();
    const img13_vini = new Image();

    img1_bubi.src = '/assets/images/cards/bubi-2.png';
    img2_bubi.src = '/assets/images/cards/bubi-3.png';
    img3_bubi.src = '/assets/images/cards/bubi-4.png';
    img4_bubi.src = '/assets/images/cards/bubi-5.png';
    img5_bubi.src = '/assets/images/cards/bubi-6.png';
    img6_bubi.src = '/assets/images/cards/bubi-7.png';
    img7_bubi.src = '/assets/images/cards/bubi-8.png';
    img8_bubi.src = '/assets/images/cards/bubi-9.png';
    img9_bubi.src = '/assets/images/cards/bubi-d.png';
    img10_bubi.src = '/assets/images/cards/bubi-k.png';
    img11_bubi.src = '/assets/images/cards/bubi-t.png';
    img12_bubi.src = '/assets/images/cards/bubi-v.png';
    img13_bubi.src = '/assets/images/cards/bubi-x.png';

    img1_chervi.src = '/assets/images/cards/chervi-2.png';
    img2_chervi.src = '/assets/images/cards/chervi-3.png';
    img3_chervi.src = '/assets/images/cards/chervi-4.png';
    img4_chervi.src = '/assets/images/cards/chervi-5.png';
    img5_chervi.src = '/assets/images/cards/chervi-6.png';
    img6_chervi.src = '/assets/images/cards/chervi-7.png';
    img7_chervi.src = '/assets/images/cards/chervi-8.png';
    img8_chervi.src = '/assets/images/cards/chervi-9.png';
    img9_chervi.src = '/assets/images/cards/chervi-d.png';
    img10_chervi.src = '/assets/images/cards/chervi-k.png';
    img11_chervi.src = '/assets/images/cards/chervi-t.png';
    img12_chervi.src = '/assets/images/cards/chervi-v.png';
    img13_chervi.src = '/assets/images/cards/chervi-x.png';

    img1_kresti.src = '/assets/images/cards/kresti-2.png';
    img2_kresti.src = '/assets/images/cards/kresti-3.png';
    img3_kresti.src = '/assets/images/cards/kresti-4.png';
    img4_kresti.src = '/assets/images/cards/kresti-5.png';
    img5_kresti.src = '/assets/images/cards/kresti-6.png';
    img6_kresti.src = '/assets/images/cards/kresti-7.png';
    img7_kresti.src = '/assets/images/cards/kresti-8.png';
    img8_kresti.src = '/assets/images/cards/kresti-9.png';
    img9_kresti.src = '/assets/images/cards/kresti-d.png';
    img10_kresti.src = '/assets/images/cards/kresti-k.png';
    img11_kresti.src = '/assets/images/cards/kresti-t.png';
    img12_kresti.src = '/assets/images/cards/kresti-v.png';
    img13_kresti.src = '/assets/images/cards/kresti-x.png';

    img1_vini.src = '/assets/images/cards/vini-2.png';
    img2_vini.src = '/assets/images/cards/vini-3.png';
    img3_vini.src = '/assets/images/cards/vini-4.png';
    img4_vini.src = '/assets/images/cards/vini-5.png';
    img5_vini.src = '/assets/images/cards/vini-6.png';
    img6_vini.src = '/assets/images/cards/vini-7.png';
    img7_vini.src = '/assets/images/cards/vini-8.png';
    img8_vini.src = '/assets/images/cards/vini-9.png';
    img9_vini.src = '/assets/images/cards/vini-d.png';
    img10_vini.src = '/assets/images/cards/vini-k.png';
    img11_vini.src = '/assets/images/cards/vini-t.png';
    img12_vini.src = '/assets/images/cards/vini-v.png';
    img13_vini.src = '/assets/images/cards/vini-x.png';
  }
};

exports.addLoadEvent = function(func) {
  const oldonload = window.onload;
  if (typeof window.onload !== 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    };
  }
};
