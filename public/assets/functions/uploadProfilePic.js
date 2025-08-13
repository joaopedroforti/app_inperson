function uploadProfilePic(input) {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const base64 = e.target.result;

      // Substitui a imagem de visualização
      const imgElement = document.getElementById('profile_pic');
      imgElement.src = base64;

      // Define o valor no campo oculto
      document.getElementById('profile_pic_base64').value = base64;

      console.log('Base64 da imagem:', base64); // Apenas para debug
    };

    reader.readAsDataURL(file);
  }

  function resetProfilePic() {
    const defaultImg = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8lRbS7eKYzDq-Ftxc1p8G_TTw2unWBMEYUw&s';
    document.getElementById('profile_pic').src = defaultImg;
    document.getElementById('profile_pic_input').value = '';
    document.getElementById('profile_pic_base64').value = '';
  }