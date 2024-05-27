const header = document.querySelector("header");
    const hamburgerBtn = document.querySelector("#hamburger-btn");
    const closeMenuBtn = document.querySelector("#close-menu-btn");

    // Toggle mobile menu on hamburger button click
    hamburgerBtn.addEventListener("click", () => header.classList.toggle("show-mobile-menu"));

    // Close mobile menu on close button click
    closeMenuBtn.addEventListener("click", () => hamburgerBtn.click());


    window.addEventListener('scroll', function() {
        if (window.scrollY > 0) {
          header.classList.add('active'); // Agrega clase al hacer scroll
        } else {
          header.classList.remove('active'); // Remueve clase al volver arriba
        }
      });
      
      $(document).ready(function() {
        const imageWrapper = $(".images");
        const lightbox = $(".lightbox");
        const downloadImgBtn = lightbox.find(".uil-import");
        const closeImgBtn = lightbox.find(".close-icon");
    
        const downloadImg = (imgUrl) => {
            $.ajax({
                url: imgUrl,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob) {
                    const a = document.createElement("a");
                    a.href = URL.createObjectURL(blob);
                    a.download = new Date().getTime();
                    a.click();
                },
                error: function() {
                    alert("Failed to download image!");
                }
            });
        };
    
        const showLightbox = (name, img) => {
            lightbox.find("img").attr("src", img);
            lightbox.find("span").text(name);
            downloadImgBtn.attr("data-img", img);
            lightbox.addClass("show");
            $("body").css("overflow", "hidden");
        };
    
        const hideLightbox = () => {
            lightbox.removeClass("show");
            $("body").css("overflow", "auto");
        };
    
        const generateHTML = (images) => {
            const html = images.map(img =>
                `<li class="card">
                    <img onclick="showLightbox('${img.name}', '${img.url}')" src="${img.url}" alt="${img.name}">
                    <div class="details">
                        <div class="photographer">
                            <i class="uil uil-camera"></i>
                            <span>${img.name}</span>
                        </div>
                        <button onclick="downloadImg('${img.url}');">
                            <i class="uil uil-import"></i>
                        </button>
                    </div>
                </li>`
            ).join("");
            imageWrapper.html(html);
        };
    
        $.ajax({
            url: "../endpoints/img",
            method: 'GET',
            dataType: 'json',
            success: function(images) {
                generateHTML(images);
            },
            error: function() {
                alert("Failed to load images!");
            }
        });
    
        closeImgBtn.on("click", hideLightbox);
    
        downloadImgBtn.on("click", function(e) {
            downloadImg($(this).data("img"));
        });
    });


    $(document).ready(function() {
        const newsContainer = $("#news-container");

        const generateHTML = (news) => {
            return news.map(item => `
                <div class="card-item">
                    <img src="${item.imagen}" alt="Card Image">
                    <span class="${item.categoria ? item.categoria.toLowerCase() : ''}">${item.categoria}</span>
                    <h3>${item.titulo}</h3>
                    <p>${item.contenido.substring(0, 100)}...</p>
                    <a href="#" class="read-more" data-id="${item.id}">Leer más</a>
                    <div class="data" style="display:none;">Fecha: ${item.fecha}, Autor: ${item.autor}</div>
                </div>
            `).join("");
        };

        const fetchNews = () => {
            $.ajax({
                url: '../endpoints/news',
                method: 'GET',
                dataType: 'json',
                success: function(news) {
                    const html = generateHTML(news);
                    newsContainer.html(html);
                    newsContainer.find('.card-item').each(function(index) {
                        $(this).delay(index * 200).queue(function(next) {
                            $(this).addClass('visible');
                            next();
                        });
                    });
                },
                error: function() {
                    alert("Error al cargar las noticias");
                }
            });
        };

        // Show popup when "Leer más" is clicked
        $(document).on('click', '.read-more', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const cardItem = $(this).closest('.card-item');
            const title = cardItem.find('h3').text();
            const content = cardItem.find('p').text();
            const details = cardItem.find('.data').text();
            $('#popup-title').text(title);
            $('#popup-content').text(content);
            $('#popup-details').text(details);
            $('#news-popup').fadeIn();
        });

        // Close popup when close button is clicked
        $(document).on('click', '.close-btn', function() {
            $('#news-popup').fadeOut();
        });

        // Initial fetch
        fetchNews();
    });