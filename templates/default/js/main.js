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
      
    

const imageWrapper = document.querySelector(".images");
const lightbox = document.querySelector(".lightbox");
const downloadImgBtn = lightbox.querySelector(".uil-import");
const closeImgBtn = lightbox.querySelector(".close-icon");

const downloadImg = (imgUrl) => {
    // Converting received img to blob, creating its download link, & downloading it
    fetch(imgUrl).then(res => res.blob()).then(blob => {
        const a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = new Date().getTime();
        a.click();
    }).catch(() => alert("Failed to download image!"));
}

const showLightbox = (name, img) => {
    // Showing lightbox and setting img source, name and button attribute
    lightbox.querySelector("img").src = img;
    lightbox.querySelector("span").innerText = name;
    downloadImgBtn.setAttribute("data-img", img);
    lightbox.classList.add("show");
    document.body.style.overflow = "hidden";
}

const hideLightbox = () => {
    // Hiding lightbox on close icon click
    lightbox.classList.remove("show");
    document.body.style.overflow = "auto";
}

const generateHTML = (images) => {
    // Making li of all fetched images and adding them to the existing image wrapper
    imageWrapper.innerHTML = images.map(img =>
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
}

// Load images from server using PHP script
fetch("load.php")
    .then(response => response.json())
    .then(images => generateHTML(images));

// Event listener for close button
closeImgBtn.addEventListener("click", hideLightbox);

// Event listener for download button
downloadImgBtn.addEventListener("click", (e) => downloadImg(e.target.dataset.img));
