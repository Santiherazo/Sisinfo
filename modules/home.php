<?php
if (!mconfig('active')) throw new Exception('El módulo home está deshabilitado.');
?>
<link rel="stylesheet" href="dashboard/css/carousel.css">
<link rel="stylesheet" href="dashboard/css/custom.css">
<link rel="stylesheet" href="dashboard/css/layer.css">


<section class="relative overflow-hidden h-[500px] rounded-2xl shadow-lg mt-8">
    <div class="absolute inset-0 z-0">
      <div class="carousel relative w-full h-full overflow-hidden">
        <div class="list relative w-full h-full">
          <div class="item absolute inset-0 w-full h-full bg-center bg-cover opacity-100 transition-opacity duration-1000" style="background-image: url('dashboard/img/imgheader1.jpg');">
            <div class="absolute inset-0 bg-black/40"></div>
          </div>
          <div class="item absolute inset-0 w-full h-full bg-center bg-cover opacity-0 transition-opacity duration-1000" style="background-image: url('dashboard/img/imgheader2.jpg');">
            <div class="absolute inset-0 bg-black/40"></div>
          </div>
          <div class="item absolute inset-0 w-full h-full bg-center bg-cover opacity-0 transition-opacity duration-1000" style="background-image: url('dashboard/img/imgheader3.jpg');">
            <div class="absolute inset-0 bg-black/40"></div>
          </div>
          <div class="item absolute inset-0 w-full h-full bg-center bg-cover opacity-0 transition-opacity duration-1000" style="background-image: url('dashboard/img/imgheader4.jpg');">
            <div class="absolute inset-0 bg-black/40"></div>
          </div>
          <div class="item absolute inset-0 w-full h-full bg-center bg-cover opacity-0 transition-opacity duration-1000" style="background-image: url('dashboard/img/imgheader5.jpg');">
            <div class="absolute inset-0 bg-black/40"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="absolute inset-0 z-10 flex flex-col justify-center items-start text-white px-10 md:px-20 h-full">
      <div class="mb-10">
        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4">
          Investigación Científica <br />
          <span class="text-[#0A9396]">de Vanguardia</span>
        </h1>
        <div class="w-24 h-1 bg-[#E9D8A6] mb-6"></div>
        <p class="text-lg md:text-xl max-w-lg">
          Plataforma integrada para la gestión de proyectos de investigación
        </p>
      </div>
      <div class="w-full flex justify-end">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div class="bg-[#005F73] p-6 rounded-xl text-center text-white shadow-lg">
            <div class="text-3xl font-bold">15+</div>
            <div class="text-sm mt-1">Proyectos activos</div>
          </div>
          <div class="bg-[#0A9396] p-6 rounded-xl text-center text-white shadow-lg">
            <div class="text-3xl font-bold">50+</div>
            <div class="text-sm mt-1">Investigadores</div>
          </div>
          <div class="bg-[#94D2BD] p-6 rounded-xl text-center text-gray-800 shadow-lg">
            <div class="text-3xl font-bold">200+</div>
            <div class="text-sm mt-1">Publicaciones</div>
          </div>
          <div class="bg-[#E9D8A6] p-6 rounded-xl text-center text-gray-800 shadow-lg">
            <div class="text-3xl font-bold">1M+</div>
            <div class="text-sm mt-1">Datos analizados</div>
          </div>
        </div>
      </div>
    </div>
  <div class="absolute top-1/2 left-4 transform -translate-y-1/2 z-20">
  <button class="prev bg-white/80 text-gray-800 rounded-full p-2 shadow-md hover:bg-white">❮</button>
</div>
<div class="absolute top-1/2 right-4 transform -translate-y-1/2 z-20">
  <button class="next bg-white/80 text-gray-800 rounded-full p-2 shadow-md hover:bg-white">❯</button>
</div>
  <script src="dashboard/js/carousel.js"></script>
</section>


<section class="text-gray-600 body-font bg-white rounded-2xl shadow-lg mt-8">
  <div class="container px-5 py-24 mx-auto">
    <div class="flex flex-col text-center w-full mb-20">
      <h2 class="text-xs text-[#32A6D5] tracking-widest font-medium title-font mb-1">LABORATORIO</h2>
      <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-[#003049]">Áreas de Investigación</h1>
      <p class="lg:w-2/3 mx-auto leading-relaxed text-base text-gray-500">Conoce nuestras principales líneas de trabajo e innovación en neurociencia, inteligencia artificial y modelado biomédico.</p>
    </div>
    <div class="flex flex-wrap">
      <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-4 border-[#FC5C04]">
        <h2 class="text-lg sm:text-xl text-[#003049] font-medium title-font mb-2">Neurociencia Computacional</h2>
        <p class="leading-relaxed text-base mb-4">Modelado y simulación de redes neuronales para estudiar su comportamiento y funcionamiento.</p>
        <a class="text-[#32A6D5] inline-flex items-center hover:text-[#007F82] cursor-pointer">Conoce más
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            class="w-4 h-4 ml-2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"></path>
          </svg>
        </a>
      </div>
      <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-4 border-[#32A6D5]">
        <h2 class="text-lg sm:text-xl text-[#003049] font-medium title-font mb-2">IA Aplicada en Salud</h2>
        <p class="leading-relaxed text-base mb-4">Desarrollo de algoritmos de inteligencia artificial para diagnóstico y predicción de enfermedades.</p>
        <a class="text-[#32A6D5] inline-flex items-center hover:text-[#007F82] cursor-pointer">Conoce más
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            class="w-4 h-4 ml-2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"></path>
          </svg>
        </a>
      </div>
      <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-4 border-[#FBA363]">
        <h2 class="text-lg sm:text-xl text-[#003049] font-medium title-font mb-2">Big Data Biomédico</h2>
        <p class="leading-relaxed text-base mb-4">Análisis de grandes volúmenes de datos clínicos para obtener nuevos conocimientos aplicables.</p>
        <a class="text-[#32A6D5] inline-flex items-center hover:text-[#007F82] cursor-pointer">Conoce más
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            class="w-4 h-4 ml-2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"></path>
          </svg>
        </a>
      </div>
      <div class="xl:w-1/4 lg:w-1/2 md:w-full px-8 py-6 border-l-4 border-[#32A6D5]">
        <h2 class="text-lg sm:text-xl text-[#003049] font-medium title-font mb-2">Innovación en Salud Digital</h2>
        <p class="leading-relaxed text-base mb-4">Implementación de tecnologías digitales para el seguimiento y mejora de la salud pública.</p>
        <a class="text-[#32A6D5] inline-flex items-center hover:text-[#007F82] cursor-pointer">Conoce más
          <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            class="w-4 h-4 ml-2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"></path>
          </svg>
        </a>
      </div>
    </div>
    <button class="flex mx-auto mt-16 text-white bg-[#32A6D5] border-0 py-2 px-8 focus:outline-none hover:bg-[#007F82] rounded text-lg">Ver todos los proyectos</button>
  </div>
</section>




<section class="py-20 bg-[#004060] bg-[url('https://www.transparenttextures.com/patterns/hexellence.png')] bg-repeat text-white rounded-2xl shadow-lg mt-8">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-[#94D2BD] mb-12 text-center">Últimas Publicaciones</h2>
    <div class="grid gap-8 lg:grid-cols-3 sm:max-w-sm sm:mx-auto lg:max-w-full">
      <article class="relative overflow-hidden rounded-2xl border-4 border-transparent bg-white shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-[#94D2BD] transition duration-500">
      
        <span class="absolute top-4 left-4 bg-[#0A9396] text-white text-xs font-bold px-3 py-1 rounded-full shadow">Neurociencia</span>
        
        <img src="https://images.unsplash.com/photo-1581591524425-c7e0978865fc" alt="Avances en mapeo neuronal" class="object-cover w-full h-56 rounded-t-xl">
        
        <div class="p-6">
          <div class="flex items-center gap-2 mb-3 text-gray-500 text-sm">
            <span>15 Mayo 2024</span>
          </div>
          <h3 class="text-xl font-bold mb-2 text-[#003049]">Avances en mapeo neuronal</h3>
          <p class="mb-4 text-gray-600">Nuevas técnicas de imagenología para estudio de redes neuronales...</p>
          <a href="#" class="inline-block bg-[#0A9396] text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-[#007F82] transition duration-300">Leer más</a>
        </div>
      </article>
      <article class="relative overflow-hidden rounded-2xl border-4 border-transparent bg-white shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-[#94D2BD] transition duration-500">
        <span class="absolute top-4 left-4 bg-[#E9D8A6] text-[#003049] text-xs font-bold px-3 py-1 rounded-full shadow">IA Aplicada</span>
        <img src="https://images.unsplash.com/photo-1581591524425-c7e0978865fc" alt="IA y predicción de enfermedades" class="object-cover w-full h-56 rounded-t-xl">
        <div class="p-6">
          <div class="flex items-center gap-2 mb-3 text-gray-500 text-sm">
            <span>20 Abril 2024</span>
          </div>
          <h3 class="text-xl font-bold mb-2 text-[#003049]">IA y predicción de enfermedades</h3>
          <p class="mb-4 text-gray-600">Implementación de modelos de machine learning en diagnóstico precoz...</p>
          <a href="#" class="inline-block bg-[#0A9396] text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-[#007F82] transition duration-300">Leer más</a>
        </div>
      </article>
      <article class="relative overflow-hidden rounded-2xl border-4 border-transparent bg-white shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-[#94D2BD] transition duration-500">
        <span class="absolute top-4 left-4 bg-[#005F73] text-white text-xs font-bold px-3 py-1 rounded-full shadow">Big Data</span>
        <img src="https://images.unsplash.com/photo-1581591524425-c7e0978865fc" alt="Big Data en salud pública" class="object-cover w-full h-56 rounded-t-xl">
        <div class="p-6">
          <div class="flex items-center gap-2 mb-3 text-gray-500 text-sm">
            <span>10 Marzo 2024</span>
          </div>
          <h3 class="text-xl font-bold mb-2 text-[#003049]">Big Data en salud pública</h3>
          <p class="mb-4 text-gray-600">Análisis de grandes volúmenes de datos para mejorar políticas sanitarias...</p>
          <a href="#" class="inline-block bg-[#0A9396] text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-[#007F82] transition duration-300">Leer más</a>
        </div>
      </article>
    </div>
  </div>
</section>



<section class="py-20 bg-[#004060] bg-[url('https://www.transparenttextures.com/patterns/hexellence.png')] bg-repeat text-white rounded-2xl shadow-lg mt-8">  <div class="max-w-8xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center text-[#94D2BD] mb-12">Top 5 Investigaciones</h2>
    <div class="grid gap-6 lg:grid-cols-2">
      <div class="lg:py-6 lg:pr-16">
        <div class="flex">
          <div class="flex flex-col items-center mr-4">
            <div class="flex items-center justify-center w-10 h-10 bg-[#0A9396] rounded-full">
              <span class="text-white font-bold">1</span>
            </div>
            <div class="w-px h-full bg-gray-500"></div>
          </div>
          <div class="pt-1 pb-8">
            <h3 class="mb-2 text-lg font-bold text-white">Modelado de Redes Neuronales</h3>
            <p class="text-[#E9D8A6]">Departamento de Computación</p>
          </div>
        </div>

        <div class="flex">
          <div class="flex flex-col items-center mr-4">
            <div class="flex items-center justify-center w-10 h-10 bg-[#94D2BD] rounded-full">
              <span class="text-[#003049] font-bold">2</span>
            </div>
            <div class="w-px h-full bg-gray-500"></div>
          </div>
          <div class="pt-1 pb-8">
            <h3 class="mb-2 text-lg font-bold text-white">Big Data en Epidemiología</h3>
            <p class="text-[#E9D8A6]">Departamento de Salud Pública</p>
          </div>
        </div>

        <div class="flex">
          <div class="flex flex-col items-center mr-4">
            <div class="flex items-center justify-center w-10 h-10 bg-[#E9D8A6] rounded-full">
              <span class="text-[#003049] font-bold">3</span>
            </div>
            <div class="w-px h-full bg-gray-500"></div>
          </div>
          <div class="pt-1 pb-8">
            <h3 class="mb-2 text-lg font-bold text-white">Robótica Biomédica</h3>
            <p class="text-[#E9D8A6]">Departamento de Ingeniería</p>
          </div>
        </div>

        <div class="flex">
          <div class="flex flex-col items-center mr-4">
            <div class="flex items-center justify-center w-10 h-10 bg-[#0A9396] rounded-full">
              <span class="text-white font-bold">4</span>
            </div>
            <div class="w-px h-full bg-gray-500"></div>
          </div>
          <div class="pt-1 pb-8">
            <h3 class="mb-2 text-lg font-bold text-white">Bioinformática Genómica</h3>
            <p class="text-[#E9D8A6]">Departamento de Biología</p>
          </div>
        </div>

        <div class="flex">
          <div class="flex flex-col items-center mr-4">
            <div class="flex items-center justify-center w-10 h-10 bg-[#94D2BD] rounded-full">
              <span class="text-[#003049] font-bold">5</span>
            </div>
          </div>
          <div class="pt-1">
            <h3 class="mb-2 text-lg font-bold text-white">Inteligencia Artificial Clínica</h3>
            <p class="text-[#E9D8A6]">Departamento de IA Médica</p>
          </div>
        </div>
      </div>

      <div class="relative">
        <img
          class="inset-0 object-cover object-center w-full rounded shadow-lg h-96 lg:absolute lg:h-full"
          src="https://images.pexels.com/photos/2923156/pexels-photo-2923156.jpeg?_gl=1*1djcjr5*_ga*ODYxNDIzMjI4LjE3NTI2MDkwMTE.*_ga_8JE65Q40S6*czE3NTI2MDkwMTAkbzEkZzEkdDE3NTI2MDkwNzQkajU5JGwwJGgw"
          alt="Investigaciones"
        />
      </div>
    </div>
  </div>
</section>



<section class="py-20 bg-[#004060] bg-[url('https://www.transparenttextures.com/patterns/hexellence.png')] bg-repeat text-white rounded-2xl shadow-lg mt-8">  <div class="px-4 mx-auto sm:max-w-xl md:max-w-full lg:max-w-7xl md:px-24 lg:px-8">
    <div class="mx-auto mb-10 lg:max-w-xl sm:text-center">
      <p class="inline-block px-3 py-px mb-4 text-xs font-semibold tracking-wider text-[#003049] uppercase rounded-full bg-[#94D2BD]">
        Equipo de Investigación
      </p>
      <p class="text-base text-white md:text-lg">
        Conformado por investigadores de diversas disciplinas comprometidos con la innovación y el impacto social.
      </p>
    </div>
    <div class="grid gap-10 mx-auto lg:grid-cols-2 lg:max-w-screen-lg">
      <div class="grid sm:grid-cols-3 bg-[#0A9396] rounded-xl shadow hover:shadow-lg transition p-4">
        <div class="relative w-full h-48 max-h-full rounded sm:h-auto">
          <img class="absolute object-cover w-full h-full rounded" src="https://i.pravatar.cc/300?img=1" alt="Dr. Carlos Pérez" />
        </div>
        <div class="flex flex-col justify-center mt-5 sm:mt-0 sm:p-5 sm:col-span-2">
          <p class="text-lg font-bold text-[#E9D8A6]">Dr. Carlos Pérez</p>
          <p class="mb-2 text-xs text-[#003049]">Director de Neurociencia</p>
          <p class="mb-4 text-sm tracking-wide text-white">
            Investigador principal en neurociencia computacional y análisis de datos biomédicos.
          </p>
          <div class="flex items-center space-x-3">
            <a href="#" class="text-[#E9D8A6] hover:text-[#004953]">
              <span class="material-icons">article</span>
            </a>
            <a href="#" class="text-[#E9D8A6] hover:text-[#004953]">
              <span class="material-icons">link</span>
            </a>
          </div>
        </div>
      </div>
      <div class="grid sm:grid-cols-3 bg-[#005F73] rounded-xl shadow hover:shadow-lg transition p-4">
        <div class="relative w-full h-48 max-h-full rounded sm:h-auto">
          <img class="absolute object-cover w-full h-full rounded" src="https://images.pexels.com/photos/28356849/pexels-photo-28356849.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Dra. Laura Gómez" />
        </div>
        <div class="flex flex-col justify-center mt-5 sm:mt-0 sm:p-5 sm:col-span-2">
          <p class="text-lg font-bold text-[#E9D8A6]">Dra. Laura Gómez</p>
          <p class="mb-2 text-xs text-[#94D2BD]">Inteligencia Artificial Médica</p>
          <p class="mb-4 text-sm tracking-wide text-white">
            Experta en machine learning aplicado a la salud y desarrollo de algoritmos clínicos.
          </p>
          <div class="flex items-center space-x-3">
            <a href="#" class="text-[#E9D8A6] hover:text-[#004953]">
              <span class="material-icons">article</span>
            </a>
            <a href="#" class="text-[#E9D8A6] hover:text-[#004953]">
              <span class="material-icons">link</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-20 bg-[#004060] bg-[url('https://www.transparenttextures.com/patterns/hexellence.png')] bg-repeat text-white rounded-2xl shadow-lg mt-8">
    <div class="flex justify-center mb-12">
      <div class="relative inline-block">
        <div class="flex items-center justify-center w-32 h-32 rounded-full bg-[#0A9396] animate-bounce shadow-lg">
          <h2 class="text-xl font-bold text-white text-center leading-tight">Galería<br>Científica</h2>
        </div>
      </div>
    </div>
    <div class="grid gap-8 grid-cols-1 md:grid-cols-3">
      <div class="group relative overflow-hidden rounded-xl shadow-lg">
        <img src="https://images.unsplash.com/photo-1575503802870-45de6a6217c8"
            alt="Laboratorio"
            class="object-cover w-full h-80 transform group-hover:scale-110 transition duration-500" />
        <div class="absolute inset-0 bg-[#0A9396]/60 opacity-0 group-hover:opacity-100 transition duration-500 flex items-center justify-center">
          <p class="text-white font-semibold text-lg">Laboratorio</p>
        </div>
      </div>
      <div class="group relative overflow-hidden rounded-xl shadow-lg">
        <img src="https://images.unsplash.com/photo-1581591524425-c7e0978865fc"
            alt="Microscopio"
            class="object-cover w-full h-80 transform group-hover:scale-110 transition duration-500" />
        <div class="absolute inset-0 bg-[#005F73]/60 opacity-0 group-hover:opacity-100 transition duration-500 flex items-center justify-center">
          <p class="text-white font-semibold text-lg">Microscopio</p>
        </div>
      </div>
      <div class="group relative overflow-hidden rounded-xl shadow-lg">
        <img src="https://images.unsplash.com/photo-1581591524425-c7e0978865fc"
            alt="Investigación"
            class="object-cover w-full h-80 transform group-hover:scale-110 transition duration-500" />
        <div class="absolute inset-0 bg-[#003049]/60 opacity-0 group-hover:opacity-100 transition duration-500 flex items-center justify-center">
          <p class="text-white font-semibold text-lg">Investigación</p>
        </div>
      </div>
    </div>
  </div>
</section>