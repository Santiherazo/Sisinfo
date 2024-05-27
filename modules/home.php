<section class="hero-section">
    <div class="content">
        <h1>Find the right freelance service, right away</h1>
        <form action="#" class="search-form">
            <input type="text" placeholder="Search for any service..." required>
            <button class="material-symbols-outlined" type="submit">search</button>
        </form>
        <div class="popular-tags">
            Popular:
            <ul class="tags">
                <li><a href="#">Webite Design</a></li>
                <li><a href="#">Logo Design</a></li>
                <li><a href="#">WordPress</a></li>
                <li><a href="#">AI Design</a></li>
            </ul>
        </div>
    </div>
</section>

<style>
  .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 9999;
        }
        .close-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
        }
</style>

<section>
<div class="card-list" id="news-container">
</div>
<div class="popup" id="news-popup">
        <div class="close-btn">&times;</div>
        <h2 id="popup-title"></h2>
        <p id="popup-content"></p>
    </div>
</section>

<section>
<div class="blog-card">
  <input type="radio" name="select" id="tap-1" checked>
  <input type="radio" name="select" id="tap-2">
  <input type="radio" name="select" id="tap-3">
  <input type="checkbox" id="imgTap">
  <div class="sliders">
    <label for="tap-1" class="tap tap-1"></label>
    <label for="tap-2" class="tap tap-2"></label>
    <label for="tap-3" class="tap tap-3"></label>
  </div>
  <div class="inner-part">
    <label for="imgTap" class="img">
      <img class="img-1" src="profile-1.jpg">
    </label>
    <div class="contenido contenido-1">
      <span>26 de diciembre de 2017</span>
      <div class="title">Lorem Ipsum Dolor</div>
      <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo animi atque aliquid pariatur voluptatem numquam, quisquam. Neque est voluptates doloribus!</div>
      <button>Leer más</button>
    </div>
  </div>
  <div class="inner-part">
    <label for="imgTap" class="img">
      <img class="img-2" src="profile-2.jpg">
    </label>
    <div class="contenido contenido-2">
      <span>26 de diciembre de 2018</span>
      <div class="title">Lorem Ipsum Dolor</div>
      <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum eos ut consectetur numquam ullam fuga animi laudantium nobis rem molestias.</div>
      <button>Leer más</button>
    </div>
  </div>
  <div class="inner-part">
    <label for="imgTap" class="img">
      <img class="img-3" src="profile-3.jpg">
    </label>
    <div class="contenido contenido-3">
      <span>26 de diciembre de 2019</span>
      <div class="title">Lorem Ipsum Dolor</div>
      <div class="text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quod excepturi nemo commodi sint eum ipsam odit atque aliquam officia impedit.</div>
      <button>Leer más</button>
    </div>
  </div>
</div>
</section>

<section>
<div class="cont">
      <div class="icon-image">
        <div class="icon">
          <img src="images/img1.jpg" alt="" />
        </div>
        <div class="hover-image one">
          <div class="img">
            <img src="images/img1.jpg" alt="" />
          </div>
          <div class="main">
            <div class="details">
              <div class="name">David Marloo</div>
              <div class="job">Diseñador || Desarrollador</div>
            </div>
          </div>
        </div>
      </div>
      <div class="icon-image">
        <div class="icon">
          <img src="images/img2.jpg" alt="" />
        </div>
        <div class="hover-image one">
          <div class="img">
            <img src="images/img2.jpg" alt="" />
          </div>
          <div class="main">
            <div class="details">
              <div class="name">Lilly Carls</div>
              <div class="job">Blogger || Diseñador</div>
            </div>
          </div>
        </div>
      </div>
      <div class="icon-image">
        <div class="icon">
          <img src="images/img3.jpg" alt="" />
        </div>
        <div class="hover-image one">
          <div class="img">
            <img src="images/img3.jpg" alt="" />
          </div>
          <div class="main">
            <div class="details">
              <div class="name">Stephen Bald</div>
              <div class="job">Diseñador || Desarrollador</div>
            </div>
          </div>
        </div>
      </div>
      <div class="icon-image">
        <div class="icon">
          <img src="images/img4.jpg" alt="" />
        </div>
        <div class="hover-image one">
          <div class="img">
            <img src="images/img4.jpg" alt="" />
          </div>
          <div class="main">
            <div class="details">
              <div class="name">Mike Tyson</div>
              <div class="job">Fotógrafo || Youtuber</div>
            </div>
          </div>
        </div>
      </div>
      <div class="icon-image">
        <div class="icon">
          <img src="images/img5.jpg" alt="" />
        </div>
        <div class="hover-image one">
          <div class="img">
            <img src="images/img5.jpg" alt="" />
          </div>
          <div class="main">
            <div class="details">
              <div class="name">Emma Oliva</div>
              <div class="job">Desarrollador || Diseñador</div>
            </div>
          </div>
        </div>
      </div>
      <div class="icon-image last">
        <div class="icon">
          <img src="images/img6.jpeg" alt="" />
        </div>
        <div class="hover-image one">
          <div class="img">
            <img src="images/img6.jpg" alt="" />
          </div>
          <div class="main">
            <div class="details">
              <div class="name">David Marloo</div>
              <div class="job">Blogger || Youtuber</div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>